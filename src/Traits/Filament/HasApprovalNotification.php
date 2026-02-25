<?php

namespace Ffhs\Approvals\Traits\Filament;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasApprovalNotification
{
    use HasApprovalResetNotification;

    protected null|string|Closure|Notification $notificationOnChangeApproval;
    protected null|string|Closure|Notification $notificationOnSetApproval;
    protected null|string|Closure|Notification $notificationOnRemoveApproval;

    public function notificationOnChangeApproval(null|string|Closure|Notification $notificationOnChangeApproval): static
    {
        $this->notificationOnChangeApproval = $notificationOnChangeApproval;

        return $this;
    }


    public function notificationOnSetApproval(null|string|Closure|Notification $notificationOnSetApproval): static
    {
        $this->notificationOnSetApproval = $notificationOnSetApproval;

        return $this;
    }

    public function notificationOnRemoveApproval(null|string|Closure|Notification $notificationOnRemoveApproval): static
    {
        $this->notificationOnRemoveApproval = $notificationOnRemoveApproval;

        return $this;
    }

    public function sendNotificationOnRemoveApproval(string $status): void
    {
        $notification = $this->evaluate($this->notificationOnRemoveApproval, [
            'status' => $status,
        ]);

        if (is_null($notification) || $notification instanceof Notification) {
            $notification?->send();
            return;
        }

        Notification::make()
            ->title($notification)
            ->success()
            ->send();
    }

    public function sendNotificationOnChangeApproval(HasApprovalStatuses $status, HasApprovalStatuses $last): void
    {
        $notification = $this->evaluate($this->notificationOnChangeApproval, [
            'status' => $status,
            'lastStatus' => $last,
        ]);

        if (is_null($notification) || $notification instanceof Notification) {
            $notification?->send();
            return;
        }

        Notification::make()
            ->title($notification)
            ->success()
            ->send();
    }

    public function sendNotificationOnSetApproval(string $status): void
    {
        $notification = $this->evaluate($this->notificationOnSetApproval, [
            'status' => $status,
        ]);

        if (is_null($notification) || $notification instanceof Notification) {
            $notification?->send();
            return;
        }

        Notification::make()
            ->title($notification)
            ->success()
            ->send();
    }

    public function sendNotificationOnResetApproval(HasApprovalStatuses $lastStatus): void
    {
        $this->getNotificationOnResetApproval($lastStatus)?->send();
    }
}
