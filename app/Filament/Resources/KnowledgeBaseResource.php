<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KnowledgeBaseResource\Pages;
use App\Models\KnowledgeBase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class KnowledgeBaseResource extends Resource
{
    protected static ?string $model = KnowledgeBase::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = '知识库管理';
    protected static ?string $modelLabel = '知识库';
    protected static ?string $navigationGroup = '内容管理';
    protected static ?int $navigationSort = 70;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('知识库标题')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\Select::make('category')
                            ->label('分类')
                            ->options([
                                'general' => '综合',
                                'tech' => '技术',
                                'business' => '商业',
                                'other' => '其他',
                            ])
                            ->default('general'),
                    ])->columns(2),

                Forms\Components\Section::make('权限设置')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->label('公开')
                            ->helperText('公开后所有用户可见'),
                        Forms\Components\Toggle::make('is_vip_only')
                            ->label('仅 VIP')
                            ->helperText('开启后仅 VIP 用户可访问'),
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
                Tables\Columns\TextColumn::make('title')
                    ->label('标题')
                    ->searchable()
                    ->limit(40)
                    ->url(fn (KnowledgeBase $record): string => static::getUrl('edit', ['record' => $record]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('category')
                    ->label('分类')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general' => '综合',
                        'tech' => '技术',
                        'business' => '商业',
                        'other' => '其他',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('评论数')
                    ->counts('comments')
                    ->badge()
                    ->color('info')
                    ->url(fn (KnowledgeBase $record): string => CommentResource::getUrl('index', [
                        'tableFilters' => [
                            'knowledge_base_comments' => ['knowledge_base_id' => (string) $record->id],
                        ],
                    ])),
                Tables\Columns\IconColumn::make('is_public')
                    ->label('公开')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_vip_only')
                    ->label('VIP')
                    ->boolean(),
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('文档数')
                    ->counts('documents')
                    ->url(fn (KnowledgeBase $record): string => KnowledgeDocumentResource::getUrl('index', [
                        'tableFilters' => [
                            'knowledge_base_id' => ['value' => (string) $record->id],
                        ],
                    ]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => '综合',
                        'tech' => '技术',
                        'business' => '商业',
                        'other' => '其他',
                    ]),
                Tables\Filters\TernaryFilter::make('is_public')->label('公开'),
                Tables\Filters\TernaryFilter::make('is_vip_only')->label('仅 VIP'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('viewComments')
                    ->label('查看评论')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn (KnowledgeBase $record): string => CommentResource::getUrl('index', [
                        'tableFilters' => [
                            'knowledge_base_comments' => ['knowledge_base_id' => (string) $record->id],
                        ],
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('setPublic')
                        ->label('设为公开')
                        ->icon('heroicon-o-globe-alt')
                        ->requiresConfirmation()
                        ->modalHeading('批量设为公开')
                        ->modalDescription('选中的知识库将对所有用户可见')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_public' => true]);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('✅ 已设为公开')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('setPrivate')
                        ->label('设为私有')
                        ->icon('heroicon-o-lock-closed')
                        ->requiresConfirmation()
                        ->modalHeading('批量设为私有')
                        ->modalDescription('选中的知识库将仅管理员可见')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_public' => false]);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('✅ 已设为私有')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('setVip')
                        ->label('设为 VIP 专属')
                        ->icon('heroicon-o-star')
                        ->requiresConfirmation()
                        ->modalHeading('批量设为 VIP 专属')
                        ->modalDescription('选中的知识库将仅 VIP 用户可访问')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_vip_only' => true]);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('✅ 已设为 VIP 专属')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgeBases::route('/'),
            'create' => Pages\CreateKnowledgeBase::route('/create'),
            'edit' => Pages\EditKnowledgeBase::route('/{record}/edit'),
        ];
    }
}
