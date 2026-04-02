<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\EmailSubscription;
use App\Services\SubscriptionDigestMailer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * 后台「订阅」对应前台邮件订阅（email_subscriptions / EmailSubscription）。
 * 付费会员套餐请使用 Subscription 模型另建资源，勿与此混用。
 */
class SubscriptionResource extends Resource
{
    protected static ?string $model = EmailSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = '邮件订阅';

    protected static ?string $modelLabel = '邮件订阅';

    protected static ?string $pluralModelLabel = '邮件订阅';

    protected static ?string $navigationGroup = '订阅与会员';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('订阅信息')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('关联用户')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\TextInput::make('email')
                            ->label('邮箱')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('subscribed_to_daily')
                            ->label('每日日报')
                            ->default(true),
                        Forms\Components\Toggle::make('subscribed_to_weekly')
                            ->label('每周汇总')
                            ->default(true),
                        Forms\Components\Toggle::make('subscribed_to_notifications')
                            ->label('系统通知邮件')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('退订')
                    ->schema([
                        Forms\Components\TextInput::make('unsubscribe_token')
                            ->label('退订令牌')
                            ->disabled()
                            ->dehydrated(false)
                            ->hiddenOn('create'),
                        Forms\Components\DateTimePicker::make('unsubscribed_at')
                            ->label('全局退订时间')
                            ->nullable()
                            ->helperText('非空表示用户曾一键退订全部；清空可视为重新激活（需同时打开上方开关）'),
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('用户')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('subscribed_to_daily')
                    ->label('日报')
                    ->boolean(),
                Tables\Columns\IconColumn::make('subscribed_to_weekly')
                    ->label('周报')
                    ->boolean(),
                Tables\Columns\IconColumn::make('subscribed_to_notifications')
                    ->label('通知')
                    ->boolean(),
                Tables\Columns\TextColumn::make('unsubscribed_at')
                    ->label('退订时间')
                    ->dateTime('Y-m-d H:i')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('subscribed_to_daily')
                    ->label('日报'),
                Tables\Filters\TernaryFilter::make('subscribed_to_weekly')
                    ->label('周报'),
                Tables\Filters\Filter::make('active')
                    ->label('仅活跃订阅')
                    ->query(fn (Builder $q): Builder => $q->whereNull('unsubscribed_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('send_digest_now')
                    ->label('立即发送')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('发送订阅邮件')
                    ->modalDescription('向该邮箱发送一封与「定时任务」相同规则的邮件（非周一为日报；须未退订且至少开启日报或周报之一）。')
                    ->action(function (EmailSubscription $record): void {
                        $mailer = app(SubscriptionDigestMailer::class);
                        $result = $mailer->runForSingleEmail($record->email);
                        $body = implode("\n", $result['lines']);
                        if ($result['failed'] > 0 && $result['sent'] === 0) {
                            Notification::make()
                                ->title('发送失败')
                                ->body($body)
                                ->danger()
                                ->send();

                            return;
                        }
                        if ($result['sent'] > 0) {
                            Notification::make()
                                ->title('已发送')
                                ->body($body)
                                ->success()
                                ->send();

                            return;
                        }
                        Notification::make()
                            ->title('未发送')
                            ->body($body)
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('unsubscribe_link')
                    ->label('退订链接')
                    ->icon('heroicon-m-link')
                    ->url(fn (EmailSubscription $record): string => $record->getUnsubscribeUrl())
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
