<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasCasesOnSetApprovalNotifications
{
    protected ?Closure $caseNotificationOnSetApprovalUsing = null;

    /** @var array<string, null|string|Closure|Notification> */
    protected array|Closure $casesNotificationOnSetApproval = [];

    public function caseNotificationOnSetApprovalUsing(Closure $caseNotificationOnSetApprovalUsing): static
    {
        $this->caseNotificationOnSetApprovalUsing = $caseNotificationOnSetApprovalUsing;
        return $this;
    }

    /**
     * @param array<string, null|string|Closure|Notification> $casesNotificationOnSetApproval
     * @return $this
     */
    public function casesNotificationOnSetApproval(array|Closure $casesNotificationOnSetApproval): static
    {
        $this->casesNotificationOnSetApproval = $casesNotificationOnSetApproval;
        return $this;
    }

    public function caseNotificationOnSetApproval(
        HasApprovalStatuses $actionCase,
        null|string|Closure|Notification $notification
    ): static {
        if (is_null($this->caseNotificationOnSetApprovalUsing)) {
            $this->caseNotificationOnSetApprovalUsing = null;
        }

        $this->casesNotificationOnSetApproval[$actionCase->value] = $notification;

        return $this;
    }

    public function getCaseNotificationOnSetApproval(HasApprovalStatuses $actionCase): ?Notification
    {
        return $this->getCaseNotification(
            $actionCase,
            $this->caseNotificationOnSetApprovalUsing,
            $this->casesNotificationOnSetApproval
        );
    }
}
