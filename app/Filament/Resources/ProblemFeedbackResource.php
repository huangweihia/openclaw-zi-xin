<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProblemFeedbackResource\Pages;
use App\Models\ProblemFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ProblemFeedbackResource extends Resource
{
    protected static ?string $model = ProblemFeedback::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = '问题反馈';
    protected static ?string $modelLabel = '问题反馈';
    protected static ?string $pluralModelLabel = '问题反馈';
    protected static ?string $navigationGroup = '运营与数据';
    protected static ?int $navigationSort = 17;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->label('标题')->disabled()->columnSpanFull(),
            Forms\Components\Textarea::make('content')->label('反馈内容')->rows(8)->disabled()->columnSpanFull(),
            Forms\Components\Placeholder::make('image_preview')
                ->label('截图')
                ->content(function (?ProblemFeedback $record): string {
                    if (! $record || ! $record->imageUrl()) {
                        return '无截图';
                    }

                    return $record->imageUrl();
                }),
            Forms\Components\Select::make('status')
                ->label('审核状态')
                ->options([
                    'pending' => '待审核',
                    'approved' => '已采纳',
                    'rejected' => '已拒绝',
                ])
                ->required(),
            Forms\Components\Textarea::make('review_note')
                ->label('审核备注')
                ->rows(4)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('提交人')->searchable(),
                Tables\Columns\TextColumn::make('title')->label('标题')->limit(38)->searchable(),
                Tables\Columns\ImageColumn::make('image_path')->label('截图')->disk('public'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => [
                        'pending' => '待审核',
                        'approved' => '已采纳',
                        'rejected' => '已拒绝',
                    ][$state] ?? $state),
                Tables\Columns\TextColumn::make('created_at')->label('提交时间')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('状态')->options([
                    'pending' => '待审核',
                    'approved' => '已采纳',
                    'rejected' => '已拒绝',
                ]),
            ])
            ->actions([
                Action::make('approve')
                    ->label('采纳并奖励 1 天 VIP')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ProblemFeedback $record): bool => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function (ProblemFeedback $record): void {
                        $record->status = 'approved';
                        $record->reviewed_by = auth()->id();
                        $record->reviewed_at = now();

                        if (! $record->rewarded_at) {
                            $user = $record->user;
                            // 终身 VIP 在支付服务中：subscription_ends_at = null，role = vip。
                            // 月/年 VIP 也会是 role=vip，但 subscription_ends_at 会有到期时间，应在采纳后 +1 天。
                            $isLifetimeVip = $user->role === 'vip' && is_null($user->subscription_ends_at);

                            if ($isLifetimeVip) {
                                // 终身不改写到期时间，但仍记录奖励时间，避免重复奖励
                                $record->rewarded_at = now();
                            } else {
                                $base = $user->subscription_ends_at && $user->subscription_ends_at->isFuture()
                                    ? $user->subscription_ends_at
                                    : now();
                                $user->subscription_ends_at = $base->copy()->addDay();
                                $user->save();
                                $record->rewarded_at = now();
                            }
                        }

                        $record->save();

                        Notification::make()->success()->title('已采纳并奖励 1 天 VIP')->send();
                    }),
                Action::make('reject')
                    ->label('拒绝')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ProblemFeedback $record): bool => $record->status !== 'rejected')
                    ->requiresConfirmation()
                    ->action(function (ProblemFeedback $record): void {
                        $record->status = 'rejected';
                        $record->reviewed_by = auth()->id();
                        $record->reviewed_at = now();
                        $record->save();
                        Notification::make()->success()->title('已标记为拒绝')->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemFeedback::route('/'),
            'edit' => Pages\EditProblemFeedback::route('/{record}/edit'),
        ];
    }
}

