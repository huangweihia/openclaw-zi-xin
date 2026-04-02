<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdSlotResource\Pages;
use App\Models\AdSlot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdSlotResource extends Resource
{
    protected static ?string $model = AdSlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = '广告位';

    protected static ?string $modelLabel = '广告位';

    protected static ?string $pluralModelLabel = '广告位';

    protected static ?string $navigationGroup = '运营与数据';

    protected static ?int $navigationSort = 16;

    public static function canCreate(): bool
    {
        return static::getModel()::query()->count() === 0;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('前台右侧边栏广告位')
                ->description('宽屏（≥1400px）固定在视口右侧留白（主内容区外）；中等桌面仍在主内容栏内右侧。图片与外链 URL 二选一，保存时自动互斥。关闭广告仅隐藏当前页，刷新后仍会出现。')
                ->schema([
                    Forms\Components\Toggle::make('is_enabled')
                        ->label('启用广告位')
                        ->default(false),
                    Forms\Components\Select::make('display_mode')
                        ->label('展示方式')
                        ->options([
                            'standard' => '图文卡片（标题+正文+可选图片+按钮）',
                            'html' => '自定义 HTML（高级）',
                        ])
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->maxLength(255)
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\Textarea::make('body')
                        ->label('文字说明')
                        ->rows(5)
                        ->helperText('有图片时显示在图片下方；无图片时单独展示。')
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\FileUpload::make('image_path')
                        ->label('上传图片')
                        ->image()
                        ->maxFiles(1)
                        ->nullable()
                        ->deletable()
                        ->reorderable(false)
                        // 避免每次打开表单都去磁盘 exists 检查；上传失败/异常时减少 Filepond 卡在「加载中」
                        ->fetchFileInformation(false)
                        ->disk('public_web')
                        ->directory('avatars/ad-slots')
                        ->visibility('public')
                        ->maxSize(3072)
                        ->removeUploadedFileButtonPosition('right')
                        ->helperText('与「图片外链」二选一；文件保存在 public/avatars/ad-slots（与头像同目录树，一般与 /avatars/ 静态规则一致）。若上传失败无法取消，请开启下方「清空当前图片并重传」后保存。')
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\Toggle::make('clear_image')
                        ->label('清空当前图片并重传')
                        ->helperText('开启后保存会删除已上传图片并清空图片外链。')
                        ->default(false)
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\TextInput::make('image_url')
                        ->label('图片外链 URL')
                        ->url()
                        ->maxLength(2048)
                        ->helperText('与「上传图片」二选一；填写后会删除已上传文件并清空本地上传。')
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\TextInput::make('cta_label')
                        ->label('按钮文案')
                        ->maxLength(100)
                        ->placeholder('查看详情')
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\TextInput::make('link_url')
                        ->label('按钮/图片跳转链接（可选）')
                        ->url()
                        ->maxLength(2048)
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'standard'),
                    Forms\Components\Textarea::make('html_content')
                        ->label('HTML 内容')
                        ->rows(8)
                        ->columnSpanFull()
                        ->visible(fn (Get $get): bool => $get('display_mode') === 'html'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\IconColumn::make('is_enabled')->label('启用')->boolean(),
                Tables\Columns\TextColumn::make('display_mode')->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAdSlots::route('/'),
            'edit' => Pages\EditAdSlot::route('/{record}/edit'),
        ];
    }
}
