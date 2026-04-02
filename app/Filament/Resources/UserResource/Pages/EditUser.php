<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Services\EmailNotificationService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = '编辑用户';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendVipExpiryReminder')
                ->label('发送 VIP 到期提醒')
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->visible(fn (): bool => $this->record instanceof User && $this->record->isVipExpiryWithinDays(3))
                ->requiresConfirmation()
                ->modalHeading('发送 VIP 到期提醒邮件')
                ->modalDescription('将向该用户邮箱发送「VIP 到期提醒」模板邮件。')
                ->action(function (): void {
                    /** @var User $user */
                    $user = $this->record;
                    $days = $user->subscriptionDaysRemaining() ?? 0;
                    $ok = app(EmailNotificationService::class)->sendFromTemplateByKey(
                        'vip_expiry_reminder',
                        $user,
                        [
                            'recipient_name' => $user->name,
                            'expiry_date' => $user->subscription_ends_at?->format('Y-m-d H:i') ?? '',
                            'days_remaining' => (string) $days,
                        ],
                        'vip_expiry_reminder'
                    );

                    if ($ok) {
                        Notification::make()
                            ->title('邮件已发送')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('发送失败')
                            ->body('请检查邮件模板与 SMTP 配置。')
                            ->danger()
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
