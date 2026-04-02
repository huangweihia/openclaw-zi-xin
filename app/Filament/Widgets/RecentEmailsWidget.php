<?php

namespace App\Filament\Widgets;

use App\Models\EmailLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentEmailsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(EmailLog::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('recipient')
                    ->label('收件人')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('主题')
                    ->limit(50),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sent' => '✅ 已发送',
                        'failed' => '❌ 失败',
                        default => '⏳ 待发送',
                    }),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('发送时间')
                    ->dateTime('Y-m-d H:i'),
            ])
            ->actions([])
            ->bulkActions([])
            ->paginated(false);
    }
}
