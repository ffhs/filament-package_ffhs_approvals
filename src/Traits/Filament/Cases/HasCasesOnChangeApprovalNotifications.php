<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasCasesOnChangeApprovalNotifications
{
    protected ?Closure $caseNotificationOnChangeApprovalUsing = null;
    /** @var array<string, null|string|Closure|Notification> */
    protected array|Closure $casesNotificationOnChangeApproval = [];

    public function caseNotificationOnChangeApprovalUsing(Closure $caseNotificationOnChangeApprovalUsing): static
    {
        $this->caseNotificationOnChangeApprovalUsing = $caseNotificationOnChangeApprovalUsing;
        return $this;
    }

    /**
     * @param array<string, null|string|Closure|Notification> $casesNotificationOnChangeApproval
     * @return $this
     */
    public function casesNotificationOnChangeApproval(array|Closure $casesNotificationOnChangeApproval): static
    {
        $this->casesNotificationOnChangeApproval = $casesNotificationOnChangeApproval;
        return $this;
    }

    public function caseNotificationOnChangeApproval(
        HasApprovalStatuses $actionCase,
        null|string|Closure|Notification $notification
    ): static {
        if (!is_null($this->caseNotificationOnChangeApprovalUsing)) {
            $this->caseNotificationOnChangeApprovalUsing = null;
        }

        $this->casesNotificationOnChangeApproval[$actionCase->value] = $notification;

        return $this;
    }

    public function getCaseNotificationOnChangeApproval(
        HasApprovalStatuses $actionCase,
        string $lastStatus
    ): ?Notification {
        return $this->getCaseNotification(
            $actionCase,
            $this->caseNotificationOnChangeApprovalUsing,
            $this->casesNotificationOnChangeApproval,
            ['lastStatus' => $lastStatus,]
        );
    }
}
