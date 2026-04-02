<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KnowledgeDocumentResource\Pages;
use App\Models\KnowledgeDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KnowledgeDocumentResource extends Resource
{
    protected static ?string $model = KnowledgeDocument::class;

    /** 仅从知识库列表「文档数」跳转进入，不在侧边栏显示 */
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = '知识库文档';

    protected static ?string $pluralModelLabel = '知识库文档';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('knowledge_base_id')
                    ->label('所属知识库')
                    ->relationship('knowledgeBase', 'title')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('title')
                    ->label('文档标题')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->label('正文')
                    ->rows(12)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('file_type')
                    ->label('文件类型')
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('knowledgeBase.title')
                    ->label('知识库')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('文档标题')
                    ->searchable()
                    ->limit(45),
                Tables\Columns\TextColumn::make('file_type')
                    ->label('类型')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->label('阅读')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('knowledge_base_id')
                    ->label('所属知识库')
                    ->relationship('knowledgeBase', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListKnowledgeDocuments::route('/'),
            'edit' => Pages\EditKnowledgeDocument::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
