<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasApprovalResetNotification
{
    protected null|string|Closure|Notification $notificationOnResetApproval;


    public function notificationOnResetApproval(null|string|Closure|Notification $notificationOnResetApproval): static
    {
        $this->notificationOnResetApproval = $notificationOnResetApproval;

        return $this;
    }

    public function getNotificationOnResetApproval(HasApprovalStatuses $lastStatus): Notification|null
    {
        $notification = $this->evaluate($this->notificationOnResetApproval, [
            'lastStatus' => $lastStatus,
            'lastStatusLabel' => $lastStatus::getCaseLabel($lastStatus),
        ]);

        if (is_null($notification) || $notification instanceof Notification) {
            return $notification;
        }

        return Notification::make()
            ->title($notification)
            ->success();
    }
}
