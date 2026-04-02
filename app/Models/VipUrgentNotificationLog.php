<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VipUrgentNotificationLog extends Model
{
    protected $fillable = [
        'sender_user_id',
        'recipient_user_id',
        'profile_message_id',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function recipientUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function profileMessage(): BelongsTo
    {
        return $this->belongsTo(ProfileMessage::class);
    }
}
