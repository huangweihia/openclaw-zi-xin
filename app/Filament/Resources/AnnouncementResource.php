<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationLabel = '顶部公告';

    protected static ?string $modelLabel = '公告';

    protected static ?string $pluralModelLabel = '公告';

    protected static ?string $navigationGroup = '运营与数据';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('公告内容')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('slug')
                        ->label('URL 别名')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('可留空，将据标题自动生成；用于详情页地址'),
                    Forms\Components\TextInput::make('marquee_text')
                        ->label('滚动条文案')
                        ->maxLength(500)
                        ->helperText('留空则顶部滚动使用标题'),
                    Forms\Components\RichEditor::make('body')
                        ->label('详情页正文')
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('启用')
                        ->default(false),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('排序')
                        ->numeric()
                        ->default(0),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('发布时间')
                        ->nullable()
                        ->helperText('留空表示立即生效（需勾选启用）'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('slug')->copyable(),
                Tables\Columns\IconColumn::make('is_active')->label('启用')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->placeholder('—'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
