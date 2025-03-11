<?php

namespace Ffhs\Approvals\Traits;

use Closure;
use Filament\Notifications\Notification;

trait HasApprovalNotification
{
    protected null|string|Closure|Notification $notificationOnChangeApproval;
    protected null|string|Closure|Notification $notificationOnResetApproval;
    protected null|string|Closure|Notification $notificationOnSetApproval;

    public function notificationOnChangeApproval(null|string|Closure|Notification $notificationOnChangeApproval): static
    {
        $this->notificationOnChangeApproval = $notificationOnChangeApproval;

        return $this;
    }

    public function notificationOnResetApproval(null|string|Closure|Notification $notificationOnResetApproval): static
    {
        $this->notificationOnResetApproval = $notificationOnResetApproval;

        return $this;
    }

    public function notificationOnSetApproval(null|string|Closure|Notification $notificationOnSetApproval): static
    {
        $this->notificationOnSetApproval = $notificationOnSetApproval;

        return $this;
    }


    public function sendNotificationOnChangeApproval(string $status, string $last): void
    {
        $notification = $this->evaluate($this->notificationOnChangeApproval, [
            'status' => $status,
            'lastStatus' => $last,
        ]);

        if ($notification == null) {
            return;
        }

        if ($notification instanceof Notification) {
            $notification->send();
            return;
        }

        Notification::make()
            ->title($notification)
            ->success()
            ->send();
    }

    public function sendNotificationOnResetApproval(string $last): void
    {
        $notification = $this->evaluate($this->notificationOnResetApproval, [
            'lastStatus' => $last,
        ]);

        if ($notification == null) {
            return;
        }

        if ($notification instanceof Notification) {
            $notification->send();
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

        if ($notification == null) {
            return;
        }

        if ($notification instanceof Notification) {
            $notification->send();
            return;
        }

        Notification::make()
            ->title($notification)
            ->success()
            ->send();
    }
}
