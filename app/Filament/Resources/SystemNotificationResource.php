<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemNotificationResource\Pages;
use App\Models\EmailSubscription;
use App\Models\SystemNotification;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemNotificationResource extends Resource
{
    protected static ?string $model = SystemNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationLabel = '系统通知';

    protected static ?string $modelLabel = '系统通知';

    protected static ?string $pluralModelLabel = '系统通知';

    protected static ?string $navigationGroup = '用户与互动';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('发送给用户')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('接收用户')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->rules([
                            function (string $attribute, $value, \Closure $fail): void {
                                $user = User::query()->find($value);
                                if ($user && ! EmailSubscription::wantsSystemNotifications($user)) {
                                    $fail('该用户未在「邮件订阅偏好」中开启系统通知，无法创建站内通知。');
                                }
                            },
                        ]),
                    Forms\Components\TextInput::make('title')
                        ->label('标题')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('body')
                        ->label('正文')
                        ->rows(5)
                        ->columnSpanFull(),
                ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('用户')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('类型')->badge(),
                Tables\Columns\TextColumn::make('title')->label('标题')->limit(36),
                Tables\Columns\IconColumn::make('is_from_admin')->label('官方')->boolean(),
                Tables\Columns\TextColumn::make('read_at')->label('已读时间')->dateTime()->placeholder('未读'),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_from_admin')->label('仅官方'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemNotifications::route('/'),
            'create' => Pages\CreateSystemNotification::route('/create'),
        ];
    }
}
