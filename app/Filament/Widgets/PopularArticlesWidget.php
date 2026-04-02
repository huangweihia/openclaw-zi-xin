<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;

class PopularArticlesWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Article::query()
                    ->where('is_published', true)
                    ->orderBy('view_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->width(50),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->limit(50)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('category.name')
                    ->label('分类')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('view_count')
                    ->label('阅读量')
                    ->sortable()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('like_count')
                    ->label('点赞')
                    ->sortable()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('发布时间')
                    ->dateTime('m-d H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('view_all')
                    ->label('查看全部')
                    ->url(route('filament.admin.resources.articles.index')),
            ])
            ->paginated(false);
    }
}
