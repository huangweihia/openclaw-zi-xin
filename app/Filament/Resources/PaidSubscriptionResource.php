<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaidSubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * 付费 VIP 订阅（subscriptions 表）。与邮件列表（EmailSubscription）区分。
 */
class PaidSubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $slug = 'paid-subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = '付费会员';

    protected static ?string $modelLabel = '付费订阅';

    protected static ?string $pluralModelLabel = '付费订阅';

    protected static ?string $navigationGroup = '订阅与会员';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('订单信息')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('用户')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('plan')
                            ->label('套餐')
                            ->options([
                                'monthly' => '月度会员',
                                'yearly' => '年度会员',
                                'lifetime' => '终身会员',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('amount')
                            ->label('金额（元）')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options([
                                'pending' => '待支付',
                                'active' => '已生效',
                                'expired' => '已过期',
                                'cancelled' => '已取消',
                            ])
                            ->default('pending')
                            ->required()
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('时间与支付')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('开始时间'),
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('到期时间')
                            ->helperText('终身会员可留空'),
                        Forms\Components\TextInput::make('payment_id')
                            ->label('支付流水号')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('payment_method')
                            ->label('支付方式')
                            ->placeholder('alipay / wechat / manual 等')
                            ->maxLength(50),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan')
                    ->label('套餐')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'yearly' => '年度',
                        'lifetime' => '终身',
                        default => '月度',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('金额')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'expired' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '待支付',
                        'active' => '已生效',
                        'expired' => '已过期',
                        'cancelled' => '已取消',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('到期')
                    ->dateTime('Y-m-d')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('支付方式')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'pending' => '待支付',
                        'active' => '已生效',
                        'expired' => '已过期',
                        'cancelled' => '已取消',
                    ]),
                Tables\Filters\SelectFilter::make('plan')
                    ->label('套餐')
                    ->options([
                        'monthly' => '月度',
                        'yearly' => '年度',
                        'lifetime' => '终身',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('extend')
                    ->label('延长')
                    ->icon('heroicon-m-calendar')
                    ->color('success')
                    ->visible(fn (Subscription $record): bool => $record->status === 'active' && $record->plan !== 'lifetime')
                    ->form([
                        Forms\Components\TextInput::make('days')
                            ->label('延长天数')
                            ->numeric()
                            ->default(30)
                            ->required()
                            ->minValue(1),
                    ])
                    ->action(function (Subscription $record, array $data): void {
                        $days = (int) $data['days'];
                        $base = $record->expires_at ?? now();
                        $record->update([
                            'expires_at' => $base->copy()->addDays($days),
                        ]);
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListPaidSubscriptions::route('/'),
            'create' => Pages\CreatePaidSubscription::route('/create'),
            'edit' => Pages\EditPaidSubscription::route('/{record}/edit'),
        ];
    }
}
