<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserActionResource\Pages;
use App\Models\UserAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserActionResource extends Resource
{
    protected static ?string $model = UserAction::class;
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = '行为日志';
    protected static ?string $modelLabel = '行为';
    protected static ?string $navigationGroup = '用户与互动';
    protected static ?int $navigationSort = 30;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('行为信息')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('用户')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->disabled(),
                    Forms\Components\TextInput::make('type')
                        ->label('行为类型')
                        ->disabled(),
                    Forms\Components\Select::make('actionable_type')
                        ->label('对象类型')
                        ->options([
                            'App\\Models\\Article' => '文章',
                            'App\\Models\\Project' => '项目',
                        ])
                        ->disabled(),
                    Forms\Components\TextInput::make('actionable_id')
                        ->label('对象ID')
                        ->numeric()
                        ->disabled(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('行为')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'like' => 'danger',
                        'favorite' => 'warning',
                        'unlock' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'like' => '点赞',
                        'favorite' => '收藏',
                        'unlock' => '解锁',
                        default => (string) $state,
                    }),
                Tables\Columns\TextColumn::make('actionable_type')
                    ->label('对象')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'App\\Models\\Article' => '文章',
                        'App\\Models\\Project' => '项目',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('target_title')
                    ->label('目标内容')
                    ->getStateUsing(function (UserAction $record): string {
                        $target = $record->actionable;
                        if (!$target) {
                            return '内容已删除';
                        }

                        return (string) ($target->title ?? $target->name ?? '未命名内容');
                    })
                    ->limit(24),
                Tables\Columns\TextColumn::make('actionable_id')
                    ->label('对象ID'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('行为类型')
                    ->options([
                        'like' => '点赞',
                        'favorite' => '收藏',
                        'unlock' => '解锁',
                    ]),
                Tables\Filters\SelectFilter::make('actionable_type')
                    ->label('对象类型')
                    ->options([
                        'App\\Models\\Article' => '文章',
                        'App\\Models\\Project' => '项目',
                    ]),
                Tables\Filters\Filter::make('recent')
                    ->label('近7天')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('delete_target_missing')
                        ->label('删除目标已失效行为')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (): void {
                            UserAction::query()
                                ->with('actionable')
                                ->get()
                                ->filter(fn (UserAction $action) => $action->actionable === null)
                                ->chunk(200)
                                ->each(fn ($chunk) => UserAction::whereIn('id', $chunk->pluck('id'))->delete());
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserActions::route('/'),
        ];
    }
}
