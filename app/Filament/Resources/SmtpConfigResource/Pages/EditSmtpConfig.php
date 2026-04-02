<?php

namespace App\Filament\Resources\SmtpConfigResource\Pages;

use App\Filament\Resources\SmtpConfigResource;
use App\Models\SmtpConfig;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSmtpConfig extends EditRecord
{
    protected static string $resource = SmtpConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // 加载所有 SMTP 配置到表单
        $configs = SmtpConfig::getSmtpConfig();
        return array_merge($configs, $data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 保存每个配置项
        $fields = [
            'smtp_host' => ['desc' => 'SMTP 服务器', 'encrypted' => false],
            'smtp_port' => ['desc' => 'SMTP 端口', 'encrypted' => false],
            'smtp_encryption' => ['desc' => '加密方式', 'encrypted' => false],
            'smtp_username' => ['desc' => 'SMTP 用户名', 'encrypted' => false],
            'smtp_password' => ['desc' => 'SMTP 密码/授权码', 'encrypted' => true],
            'smtp_from_address' => ['desc' => '发件邮箱', 'encrypted' => false],
            'smtp_from_name' => ['desc' => '发件人名称', 'encrypted' => false],
        ];

        foreach ($fields as $key => $config) {
            if (isset($data[$key])) {
                SmtpConfig::set($key, $data[$key], $config['desc'], $config['encrypted']);
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('✅ SMTP 配置已保存')
            ->body('建议发送测试邮件验证配置是否正确')
            ->success()
            ->send();
    }
}
