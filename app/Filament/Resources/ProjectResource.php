<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = '项目管理';
    protected static ?string $modelLabel = '项目';
    protected static ?string $navigationGroup = '内容管理';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('基本信息')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('项目名称')
                        ->required()
                        ->maxLength(200),
                    Forms\Components\Textarea::make('description')
                        ->label('项目描述')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_vip')
                        ->label('VIP 专属')
                        ->default(false),
                    Forms\Components\Select::make('status')
                        ->label('状态')
                        ->options([
                            'planning' => '规划中',
                            'in_progress' => '进行中',
                            'completed' => '已完成',
                            'on_hold' => '已暂停',
                        ])
                        ->default('planning')
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('财务信息')
                ->schema([
                    Forms\Components\TextInput::make('budget')
                        ->label('预算')
                        ->numeric()
                        ->prefix('¥'),
                    Forms\Components\TextInput::make('revenue')
                        ->label('收入')
                        ->numeric()
                        ->prefix('¥'),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('开始日期'),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('结束日期'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('项目名称')->searchable(),
                Tables\Columns\IconColumn::make('is_vip')
                    ->label('VIP')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('评论数')
                    ->counts('comments')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'on_hold' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planning' => '规划中',
                        'in_progress' => '进行中',
                        'completed' => '已完成',
                        'on_hold' => '已暂停',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('revenue')->label('收入')->money('CNY')->sortable(),
                Tables\Columns\TextColumn::make('start_date')->label('开始日期')->date('Y-m-d')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态筛选')
                    ->options([
                        'planning' => '规划中',
                        'in_progress' => '进行中',
                        'completed' => '已完成',
                        'on_hold' => '已暂停',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('viewComments')
                    ->label('查看评论')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn (Project $record): string => CommentResource::getUrl('index', [
                        'tableFilters' => [
                            'commentable_type' => ['value' => Project::class],
                            'commentable_id' => ['value' => (string) $record->id],
                        ],
                    ])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
