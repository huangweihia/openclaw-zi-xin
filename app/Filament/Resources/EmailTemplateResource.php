<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = '邮件模板';
    protected static ?string $modelLabel = '邮件模板';
    protected static ?int $navigationSort = 20;
    protected static ?string $navigationGroup = '邮件系统';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('模板信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('模板名称')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('key')
                            ->label('模板键')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->helperText('用于代码中引用，如：daily_digest, weekly_summary'),
                        
                        Forms\Components\TextInput::make('subject')
                            ->label('邮件主题')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('支持变量：{{date}}, {{name}}, {{week}} 等'),
                    ])->columns(1),

                Forms\Components\Section::make('模板内容')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('邮件正文')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'link',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                            ])
                            ->helperText('支持变量：{{date}}, {{name}}, {{content}} 等'),
                        
                        Forms\Components\TagsInput::make('variables')
                            ->label('可用变量')
                            ->placeholder('添加变量名，如：date, name, content')
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('发布设置')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('启用模板')
                            ->default(true)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('模板名称')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('模板键')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('已启用')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('启用状态'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('预览')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('邮件模板预览')
                    ->modalContent(function (EmailTemplate $record) {
                        return view('filament.email-template-preview', ['template' => $record]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('关闭'),
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
            'index' => Pages\ListEmailTemplates::route('/'),
            'create' => Pages\CreateEmailTemplate::route('/create'),
            'edit' => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }
}
