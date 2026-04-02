<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FavoriteResource\Pages;
use App\Models\Favorite;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FavoriteResource extends Resource
{
    protected static ?string $model = Favorite::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = '收藏管理';
    protected static ?string $modelLabel = '收藏';
    protected static ?string $navigationGroup = '用户与互动';
    protected static ?int $navigationSort = 40;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
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
                Tables\Columns\TextColumn::make('favoritable_type')
                    ->label('对象类型')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'App\\Models\\Article' => '文章',
                        'App\\Models\\Project' => '项目',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('target_title')
                    ->label('目标内容')
                    ->getStateUsing(function (Favorite $record): string {
                        $target = $record->favoritable;
                        if (!$target) {
                            return '内容已删除';
                        }

                        return (string) ($target->title ?? $target->name ?? '未命名内容');
                    })
                    ->limit(24),
                Tables\Columns\TextColumn::make('favoritable_id')
                    ->label('对象ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('收藏时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('favoritable_type')
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
                        ->label('删除目标已失效收藏')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (): void {
                            Favorite::query()
                                ->with('favoritable')
                                ->get()
                                ->filter(fn (Favorite $favorite) => $favorite->favoritable === null)
                                ->chunk(200)
                                ->each(fn ($chunk) => Favorite::whereIn('id', $chunk->pluck('id'))->delete());
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFavorites::route('/'),
        ];
    }
}
