param(
  [string]$ProjectDir = "D:\lewan\openclaw-data\workspace\ai-side-laravel-max",
  [string]$PhpContainer = "ai_side_php_max",
  [string]$WorkerContainer = "ai_side_worker_max",
  [string]$SchedulerContainer = "ai_side_scheduler_max",
  [switch]$Seed,
  [switch]$Optimize
)

$ErrorActionPreference = "Stop"

function Info($msg) { Write-Host ("[init] " + $msg) }

function Require-Command($name) {
  if (-not (Get-Command $name -ErrorAction SilentlyContinue)) {
    throw "Missing command: $name"
  }
}

function Ensure-AppKeyQuoted([string]$envPath) {
  if (-not (Test-Path $envPath)) { return }

  $content = Get-Content -Raw -Path $envPath

  $pattern = '(?m)^(APP_KEY=)(.*)$'
  $m = [regex]::Match($content, $pattern)
  if (-not $m.Success) { return }

  $value = $m.Groups[2].Value.Trim()
  if ($value -eq "") { return }

  # If already quoted, leave it.
  if ($value.StartsWith('"') -and $value.EndsWith('"')) { return }

  # Quote base64 keys (may include '+' which can be mis-parsed by Compose env_file injection).
  if ($value.StartsWith("base64:")) {
    $escaped = $value.Replace('"', '\"')
    $newLine = "APP_KEY=`"$escaped`""
    $content2 = [regex]::Replace($content, $pattern, $newLine, 1)
    Set-Content -Path $envPath -Value $content2 -NoNewline
    Info "Quoted APP_KEY in .env"
  }
}

Require-Command docker

Info "ProjectDir=$ProjectDir"
Info "PhpContainer=$PhpContainer"

$envPath = Join-Path $ProjectDir ".env"
Ensure-AppKeyQuoted $envPath

Info "Recreate containers to reload env_file"
Push-Location $ProjectDir
try {
  docker compose up -d --force-recreate | Out-Host
} finally {
  Pop-Location
}

Info "Install PHP deps (no-dev, optimized)"
docker exec $PhpContainer sh -c "cd /var/www/html && composer install --no-dev --optimize-autoloader --no-interaction" | Out-Host

Info "Ensure Laravel storage directories"
docker exec $PhpContainer sh -c "cd /var/www/html && mkdir -p storage/framework/{cache/data,sessions,testing,views} storage/logs public/avatars" | Out-Host

Info "Fix permissions"
docker exec $PhpContainer sh -c "cd /var/www/html && chmod -R 775 storage bootstrap/cache public/avatars && chown -R www-data:www-data storage bootstrap/cache public/avatars 2>/dev/null || true" | Out-Host

Info "Generate APP_KEY if empty"
$appKey = (docker inspect $PhpContainer --format "{{range .Config.Env}}{{println .}}{{end}}" | Select-String "^APP_KEY=").ToString()
if ($appKey -match "^APP_KEY=$") {
  docker exec $PhpContainer sh -c "cd /var/www/html && php artisan key:generate --force" | Out-Host
  # Key:generate updates file, but container env may still be empty until recreate.
  Ensure-AppKeyQuoted $envPath
  Push-Location $ProjectDir
  try { docker compose up -d --force-recreate | Out-Host } finally { Pop-Location }
}

Info "Run migrations"
docker exec $PhpContainer sh -c "cd /var/www/html && php artisan migrate --force" | Out-Host

Info "Create storage link"
docker exec $PhpContainer sh -c "cd /var/www/html && php artisan storage:link --force" | Out-Host

if ($Seed) {
  Info "Seed database (DatabaseSeeder)"
  docker exec $PhpContainer sh -c "cd /var/www/html && php artisan db:seed --force" | Out-Host

  Info "Seed admin user (CreateAdminSeeder)"
  docker exec $PhpContainer sh -c "cd /var/www/html && php artisan db:seed --class=CreateAdminSeeder --force" | Out-Host
}

if ($Optimize) {
  Info "Cache config/routes/views/events"
  docker exec $PhpContainer sh -c "cd /var/www/html && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache" | Out-Host

  Info "Restart worker/scheduler to reload config"
  docker restart $WorkerContainer $SchedulerContainer | Out-Host
}

Info "Health check (HTTP 200 expected)"
docker exec $PhpContainer sh -c "curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1/; echo" | Out-Host

Info "Done"
