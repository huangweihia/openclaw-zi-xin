<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Services\SubscriptionDigestMailer;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send_scheduled_batch')
                ->label('按规则批量发送')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('批量发送订阅邮件')
                ->modalDescription('与定时任务相同：周一发周报给「开启周报」且未退订的订阅；其余日期发日报给「开启日报」且未退订的订阅。数量受限于下方上限。')
                ->form([
                    Forms\Components\TextInput::make('limit')
                        ->label('最多发送人数')
                        ->numeric()
                        ->default(100)
                        ->minValue(1)
                        ->maxValue(5000)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $limit = (int) ($data['limit'] ?? 100);
                    $mailer = app(SubscriptionDigestMailer::class);
                    $result = $mailer->runBatch($limit, null);
                    $body = implode("\n", array_slice($result['lines'], 0, 30));
                    if (count($result['lines']) > 30) {
                        $body .= "\n…（其余见邮件日志）";
                    }
                    $note = Notification::make()
                        ->title("完成：成功 {$result['sent']}，失败 {$result['failed']}")
                        ->body($body !== '' ? $body : '无详情');
                    if ($result['failed'] > 0 && $result['sent'] === 0) {
                        $note->danger();
                    } elseif ($result['sent'] === 0 && $result['failed'] === 0) {
                        $note->warning();
                    } else {
                        $note->success();
                    }
                    $note->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
