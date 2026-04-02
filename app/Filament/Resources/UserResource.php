<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\EmailNotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = '用户管理';

    protected static ?string $modelLabel = '用户';

    protected static ?string $pluralModelLabel = '用户';

    protected static ?string $navigationGroup = '用户与互动';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('账号信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('昵称')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('邮箱')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('密码')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->maxLength(255),
                        Forms\Components\Select::make('role')
                            ->label('角色')
                            ->options([
                                'user' => '普通用户',
                                'vip' => 'VIP',
                                'admin' => '管理员',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('subscription_ends_at')
                            ->label('VIP 到期时间')
                            ->seconds(false)
                            ->helperText('填写后用于 VIP 权益与到期提醒；留空表示不按时间到期。'),
                        Forms\Components\TextInput::make('avatar')
                            ->label('头像 URL')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('昵称')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('角色')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => '管理员',
                        'vip' => 'VIP',
                        default => '普通用户',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'vip' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->label('VIP 到期')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('subscription_days_remaining')
                    ->label('剩余天数')
                    ->getStateUsing(fn (User $record): string => $record->subscriptionDaysRemaining() !== null
                        ? (string) $record->subscriptionDaysRemaining() . ' 天'
                        : '—')
                    ->badge()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('最后登录')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('last_login_ip')
                    ->label('最后登录IP')
                    ->copyable()
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('角色')
                    ->options([
                        'user' => '普通用户',
                        'vip' => 'VIP',
                        'admin' => '管理员',
                    ]),
                Tables\Filters\Filter::make('vip_expiring')
                    ->label('VIP 即将到期（≤3天）')
                    ->query(fn ($query) => $query->whereNotNull('subscription_ends_at')
                        ->where('subscription_ends_at', '>', now())
                        ->where('subscription_ends_at', '<=', now()->addDays(3)->endOfDay())),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('编辑'),
                Action::make('sendVipExpiryReminder')
                    ->label('发送 VIP 到期提醒')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn (User $record): bool => $record->isVipExpiryWithinDays(3))
                    ->requiresConfirmation()
                    ->modalHeading('发送 VIP 到期提醒邮件')
                    ->modalDescription('将向该用户邮箱发送一封「VIP 到期提醒」模板邮件。')
                    ->action(function (User $record): void {
                        $days = $record->subscriptionDaysRemaining() ?? 0;
                        $ok = app(EmailNotificationService::class)->sendFromTemplateByKey(
                            'vip_expiry_reminder',
                            $record,
                            [
                                'recipient_name' => $record->name,
                                'expiry_date' => $record->subscription_ends_at?->format('Y-m-d H:i') ?? '',
                                'days_remaining' => (string) $days,
                            ],
                            'vip_expiry_reminder'
                        );

                        if ($ok) {
                            Notification::make()
                                ->title('邮件已发送')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('发送失败')
                                ->body('请检查「VIP 到期提醒」邮件模板是否存在且已启用，以及 SMTP 配置。')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
