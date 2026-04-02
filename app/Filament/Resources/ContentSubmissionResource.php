<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentSubmissionResource\Pages;
use App\Models\ContentSubmission;
use App\Services\SubmissionPublisher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ContentSubmissionResource extends Resource
{
    protected static ?string $model = ContentSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = '投稿审核';
    protected static ?string $modelLabel = '投稿';
    protected static ?string $navigationGroup = '内容管理';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('投稿信息')
                ->description('用户提交的基本信息')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->disabled()
                        ->columnSpanFull(),
                    Forms\Components\Select::make('type')
                        ->label('类型')
                        ->options([
                            'document' => '📄 文档',
                            'project' => '🚀 项目',
                            'job' => '💼 职位',
                            'knowledge' => '📚 知识库',
                        ])
                        ->disabled(),
                    Forms\Components\Textarea::make('summary')
                        ->label('摘要')
                        ->disabled()
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_paid')
                        ->label('VIP 专属内容')
                        ->helperText('开启后仅 VIP 用户可观看完整内容')
                        ->disabled(),
                ])->columns(2)->columnSpan(1),
            
            Forms\Components\Section::make('正文内容')
                ->description('投稿的完整内容（富文本）')
                ->icon('heroicon-o-pencil-square')
                ->collapsed(false)
                ->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label('正文')
                        ->disabled()
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                            'strike',
                            'underline',
                            'alignLeft',
                            'alignCenter',
                            'alignRight',
                            'alignJustify',
                        ])
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'fi-fo-rich-editor']),
                ])->columnSpanFull(),
            
            Forms\Components\Section::make('审核操作')
                ->description('审核决定和备注')
                ->icon('heroicon-o-check-circle')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('审核状态')
                        ->options([
                            'pending' => '⏳ 待审核',
                            'approved' => '✅ 已通过',
                            'rejected' => '❌ 已拒绝',
                        ])
                        ->required()
                        ->live()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('review_note')
                        ->label('审核备注')
                        ->placeholder('填写审核意见或修改建议...')
                        ->rows(4)
                        ->columnSpanFull()
                        ->helperText('拒绝时建议填写原因，方便用户修改'),
                ])->columnSpanFull(),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('type')->label('类型')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => [
                        'document' => '文档',
                        'project' => '项目',
                        'job' => '职位',
                        'knowledge' => '知识库',
                    ][$state] ?? $state),
                Tables\Columns\TextColumn::make('title')->label('标题')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('user.name')->label('投稿人')->searchable(),
                Tables\Columns\TextColumn::make('status')->label('状态')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => [
                        'pending' => '待审核',
                        'approved' => '已通过',
                        'rejected' => '已拒绝',
                    ][$state] ?? $state),
                Tables\Columns\IconColumn::make('is_paid')
                    ->label('VIP')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('提交时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => '待审核',
                        'approved' => '已通过',
                        'rejected' => '已拒绝',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'document' => '文档',
                        'project' => '项目',
                        'job' => '职位',
                        'knowledge' => '知识库',
                    ]),
                Tables\Filters\TernaryFilter::make('is_paid')
                    ->label('VIP 内容')
                    ->trueLabel('仅 VIP 内容')
                    ->falseLabel('免费内容'),
            ])
            ->actions([
                Action::make('syncPublish')
                    ->label('补发布')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('将投稿同步到对应内容表')
                    ->modalDescription('用于已通过但未生成文章/项目/职位等记录的历史数据。')
                    ->visible(fn (ContentSubmission $record): bool => $record->status === 'approved'
                        && (empty($record->published_model_id) || empty($record->published_model_type)))
                    ->action(function (ContentSubmission $record): void {
                        try {
                            SubmissionPublisher::publish($record->fresh());
                            Notification::make()
                                ->success()
                                ->title('已同步到对应内容表')
                                ->send();
                        } catch (\Throwable $e) {
                            report($e);
                            Notification::make()
                                ->danger()
                                ->title('同步失败')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContentSubmissions::route('/'),
            'edit' => Pages\EditContentSubmission::route('/{record}/edit'),
        ];
    }
}
