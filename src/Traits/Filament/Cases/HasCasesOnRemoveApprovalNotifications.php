<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasCasesOnRemoveApprovalNotifications
{
    protected ?Closure $caseNotificationOnRemoveApprovalUsing = null;
    /** @var array<string, null|string|Closure|Notification> */
    protected array|Closure $casesNotificationOnRemoveApproval = [];

    public function caseNotificationOnRemoveUsing(?Closure $caseNotificationOnRemoveApprovalUsing): static
    {
        $this->caseNotificationOnRemoveApprovalUsing = $caseNotificationOnRemoveApprovalUsing;
        return $this;
    }

    /**
     * @param array<string, null|string|Closure|Notification>|Closure $casesNotificationOnRemoveApproval
     * @return $this
     */
    public function casesNotificationOnRemoveApproval(array|Closure $casesNotificationOnRemoveApproval): static
    {
        $this->casesNotificationOnRemoveApproval = $casesNotificationOnRemoveApproval;
        return $this;
    }

    public function caseNotificationOnRemoveApproval(
        HasApprovalStatuses $actionCase,
        null|string|Closure|Notification $notification
    ): static {
        if (!is_null($this->caseNotificationOnRemoveApprovalUsing)) {
            $this->caseNotificationOnRemoveApprovalUsing = null;
        }

        $this->casesNotificationOnRemoveApproval[$actionCase->value] = $notification;

        return $this;
    }

    public function getCaseNotificationOnRemoveApproval(HasApprovalStatuses $actionCase): ?Notification
    {
        return $this->getCaseNotification(
            $actionCase,
            $this->caseNotificationOnRemoveApprovalUsing,
            $this->casesNotificationOnRemoveApproval
        );
    }
}
