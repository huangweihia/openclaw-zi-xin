<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SmtpConfigResource\Pages;
use App\Models\SmtpConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class SmtpConfigResource extends Resource
{
    protected static ?string $model = SmtpConfig::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = 'SMTP 配置';
    protected static ?string $modelLabel = 'SMTP 配置';
    protected static ?int $navigationSort = 40;
    protected static ?string $navigationGroup = '邮件系统';
    // 与「系统设置」页能力重叠：仅保留系统设置入口，隐藏 SMTP 配置列表入口
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SMTP 服务器配置')
                    ->schema([
                        Forms\Components\TextInput::make('smtp_host')
                            ->label('SMTP 服务器')
                            ->default('smtp.qq.com')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('smtp_port')
                            ->label('端口')
                            ->default(465)
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(65535),
                        
                        Forms\Components\Select::make('smtp_encryption')
                            ->label('加密方式')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                'none' => '无',
                            ])
                            ->default('ssl')
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('认证信息')
                    ->schema([
                        Forms\Components\TextInput::make('smtp_username')
                            ->label('用户名（邮箱）')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('smtp_password')
                            ->label('授权码/密码')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('QQ 邮箱请使用授权码，非登录密码'),
                    ])->columns(1),

                Forms\Components\Section::make('发件人信息')
                    ->schema([
                        Forms\Components\TextInput::make('smtp_from_address')
                            ->label('发件邮箱')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('smtp_from_name')
                            ->label('发件人名称')
                            ->default('AI 副业情报局')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('测试连接')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('testConnection')
                                ->label('📧 发送测试邮件')
                                ->requiresConfirmation()
                                ->modalHeading('发送测试邮件')
                                ->modalDescription('将发送一封测试邮件到当前配置的发件邮箱，确认继续？')
                                ->modalSubmitActionLabel('确认发送')
                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                    try {
                                        $config = [
                                            'host' => $get('smtp_host'),
                                            'port' => $get('smtp_port'),
                                            'encryption' => $get('smtp_encryption'),
                                            'username' => $get('smtp_username'),
                                            'password' => $get('smtp_password'),
                                            'from_address' => $get('smtp_from_address'),
                                            'from_name' => $get('smtp_from_name'),
                                        ];
                                        
                                        // 临时配置邮件设置
                                        config([
                                            'mail.mailers.smtp' => [
                                                'transport' => 'smtp',
                                                'host' => $config['host'],
                                                'port' => $config['port'],
                                                'encryption' => $config['encryption'] === 'none' ? null : $config['encryption'],
                                                'username' => $config['username'],
                                                'password' => $config['password'],
                                                'timeout' => 30,
                                            ],
                                            'mail.default' => 'smtp',
                                        ]);
                                        
                                        Mail::raw("这是一封测试邮件，用于验证 SMTP 配置是否正确。\n\n发送时间：" . now()->format('Y-m-d H:i:s'), function ($message) use ($config) {
                                            $message->to($config['from_address'])
                                                    ->from($config['from_address'], $config['from_name'])
                                                    ->subject('🧪 SMTP 配置测试 - AI 副业情报局');
                                        });
                                        
                                        Notification::make()
                                            ->title('✅ 测试邮件发送成功')
                                            ->body('请检查收件箱确认邮件是否收到')
                                            ->success()
                                            ->send();
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('❌ 测试失败')
                                            ->body('错误：' . $e->getMessage())
                                            ->danger()
                                            ->send();
                                    }
                                }),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('配置项')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('配置值')
                    ->formatStateUsing(fn ($record) => $record->is_encrypted ? '••••••••' : $record->value)
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_encrypted')
                    ->label('已加密')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmtpConfigs::route('/'),
            'edit' => Pages\EditSmtpConfig::route('/{record}/edit'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
