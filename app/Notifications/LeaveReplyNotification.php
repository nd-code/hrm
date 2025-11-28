<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Leave;
use App\Models\LeaveReply;

class LeaveReplyNotification extends Notification
{
    use Queueable;

    protected $leave;
    protected $reply;
    protected $senderName;

    public function __construct(Leave $leave, LeaveReply $reply, string $senderName)
    {
        $this->leave  = $leave;
        $this->reply  = $reply;
        $this->senderName = $senderName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'leave_id'   => $this->leave->id,
            'reply_id'   => $this->reply->id,
            'sender'     => $this->senderName,
            'message'    => $this->reply->message,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'leave_id'   => $this->leave->id,
            'reply_id'   => $this->reply->id,
            'sender'     => $this->senderName,
            'message'    => $this->reply->message,
        ]);
    }
}