<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = '邮件日志';
    protected static ?string $modelLabel = '邮件日志';
    protected static ?string $navigationGroup = '邮件系统';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('邮件信息')
                ->schema([
                    Forms\Components\TextInput::make('recipient')
                        ->label('收件人')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('subject')
                        ->label('主题')
                        ->required()
                        ->maxLength(500),
                    Forms\Components\Select::make('type')
                        ->label('类型')
                        ->options([
                            'job_daily' => '每日职位推荐',
                            'system' => '系统通知',
                            'notification' => '用户通知',
                        ])
                        ->default('job_daily'),
                    Forms\Components\Select::make('status')
                        ->label('状态')
                        ->options([
                            'pending' => '待发送',
                            'sent' => '已发送',
                            'failed' => '发送失败',
                        ])
                        ->default('pending'),
                ])->columns(2),

            Forms\Components\Section::make('详细内容')
                ->schema([
                    Forms\Components\Textarea::make('content')
                        ->label('内容')
                        ->rows(10)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('error_message')
                        ->label('错误信息')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\DateTimePicker::make('sent_at')
                        ->label('发送时间'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('recipient')
                    ->label('收件人')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('主题')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'job_daily' => 'primary',
                        'system' => 'warning',
                        'notification' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'job_daily' => '每日职位',
                        'system' => '系统通知',
                        'notification' => '用户通知',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => '待发送',
                        'sent' => '已发送',
                        'failed' => '失败',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('发送时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'job_daily' => '每日职位',
                        'system' => '系统通知',
                        'notification' => '用户通知',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'pending' => '待发送',
                        'sent' => '已发送',
                        'failed' => '失败',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListEmailLogs::route('/'),
            'create' => Pages\CreateEmailLog::route('/create'),
            'edit' => Pages\EditEmailLog::route('/{record}/edit'),
        ];
    }
}
