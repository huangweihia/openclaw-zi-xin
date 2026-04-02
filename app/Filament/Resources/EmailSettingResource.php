<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSettingResource\Pages;
use App\Models\EmailSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailSettingResource extends Resource
{
    protected static ?string $model = EmailSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    protected static ?string $navigationLabel = '邮件配置';
    protected static ?string $modelLabel = '邮件配置';
    protected static ?int $navigationSort = 30;
    protected static ?string $navigationGroup = '邮件系统';
    // 与「系统设置」页能力重叠：仅保留系统设置入口，隐藏邮件配置列表入口
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('邮件配置')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('配置键')
                            ->required()
                            ->maxLength(255)
                            ->disabled(),
                        Forms\Components\TextInput::make('value')
                            ->label('配置值')
                            ->required()
                            ->maxLength(500),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->rows(2)
                            ->disabled(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('配置项')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email_send_time' => '⏰ 邮件发送时间',
                        'email_recipients' => '📧 收件人列表',
                        'email_daily_enabled' => '📅 日报启用',
                        'email_weekly_enabled' => '📆 周报启用',
                        'smtp_host' => '📮 SMTP 服务器',
                        'smtp_port' => '🔌 SMTP 端口',
                        'smtp_encryption' => '🔐 加密方式',
                        'smtp_username' => '👤 SMTP 用户名',
                        'smtp_password' => '🔑 SMTP 密码',
                        'smtp_from_address' => '📤 发件邮箱',
                        'smtp_from_name' => '🏷️ 发件人名称',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('value')
                    ->label('配置值')
                    ->searchable()
                    ->limit(50)
                    ->formatStateUsing(fn (EmailSetting $record, string $state): string => match ($record->key) {
                        'smtp_password' => '••••••••',
                        'email_recipients' => count(json_decode($state, true) ?? []) . ' 个收件人',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: false),
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
            'index' => Pages\ListEmailSettings::route('/'),
            'edit' => Pages\EditEmailSetting::route('/{record}/edit'),
        ];
    }
}
