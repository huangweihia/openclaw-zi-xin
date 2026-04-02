<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobListingResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

/**
 * 前台「职位」列表与详情使用 App\Models\Job（表 positions）。
 * 与采集写入的 JobListing（job_listings）分离。
 */
class JobListingResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $slug = 'positions';

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = '职位管理';

    protected static ?string $modelLabel = '职位';

    protected static ?string $navigationGroup = '内容管理';

    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('职位信息')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('发布者')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->required()
                        ->default(fn () => auth()->id()),
                    Forms\Components\TextInput::make('title')->label('职位名称')->required()->maxLength(200),
                    Forms\Components\TextInput::make('company_name')->label('公司名称')->required()->maxLength(200),
                    Forms\Components\TextInput::make('salary_range')->label('薪资范围')->maxLength(100),
                    Forms\Components\TextInput::make('location')->label('工作地点')->maxLength(100),
                    Forms\Components\TextInput::make('source_url')->label('来源链接')->url()->maxLength(255)
                        ->helperText('招聘原文外链（最多 255 字）；前台详情展示「查看来源」'),
                ])->columns(2),

            Forms\Components\Section::make('详细内容')
                ->schema([
                    Forms\Components\Textarea::make('requirements')->label('任职要求')->rows(4)->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->label('职位描述（富文本）')
                        ->columnSpanFull()
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ]),
                ]),

            Forms\Components\Section::make('联系方式')
                ->schema([
                    Forms\Components\TextInput::make('contact_email')->label('邮箱')->email()->maxLength(255),
                    Forms\Components\TextInput::make('contact_phone')->label('电话')->tel()->maxLength(50),
                    Forms\Components\TextInput::make('contact_wechat')->label('微信')->maxLength(100),
                ])->columns(3),

            Forms\Components\Section::make('发布与权限')
                ->schema([
                    Forms\Components\Toggle::make('is_published')->label('前台展示')->default(true),
                    Forms\Components\Toggle::make('is_vip_only')->label('VIP 专属正文')->helperText('开启后非 VIP 仅见摘要'),
                    Forms\Components\Toggle::make('is_contact_vip')->label('联系方式仅 VIP 可见'),
                    Forms\Components\DateTimePicker::make('published_at')->label('发布时间'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('职位')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('company_name')->label('公司')->searchable()->limit(24),
                Tables\Columns\TextColumn::make('salary_range')->label('薪资')->toggleable(),
                Tables\Columns\TextColumn::make('location')->label('地点')->toggleable(),
                Tables\Columns\TextColumn::make('source_url')
                    ->label('来源链接')
                    ->url(fn (?string $state): ?string => $state ?: null)
                    ->openUrlInNewTab()
                    ->limit(28)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('comments_count')
                    ->label('评论')
                    ->counts('comments')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_published')->label('已发布')->boolean(),
                Tables\Columns\IconColumn::make('is_vip_only')->label('VIP正文')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('发布时间')->dateTime('Y-m-d H:i')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime('Y-m-d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('前台展示'),
                Tables\Filters\TernaryFilter::make('is_vip_only')->label('VIP 专属'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('viewComments')
                    ->label('查看评论')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn (Job $record): string => CommentResource::getUrl('index', [
                        'tableFilters' => [
                            'commentable_type' => ['value' => Job::class],
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
            'index' => Pages\ListJobListings::route('/'),
            'create' => Pages\CreateJobListing::route('/create'),
            'edit' => Pages\EditJobListing::route('/{record}/edit'),
        ];
    }
}
