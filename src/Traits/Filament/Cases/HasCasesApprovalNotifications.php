<?php

namespace Ffhs\Approvals\Traits\Filament\Cases;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Notifications\Notification;

trait HasCasesApprovalNotifications
{
    use HasCasesOnRemoveApprovalNotifications;
    use HasCasesOnSetApprovalNotifications;
    use HasCasesOnChangeApprovalNotifications;


    /**
     * @param HasApprovalStatuses $actionCase
     * @param Closure|null $using
     * @param array<string, string|Closure|Notification|null>|Closure $cases
     * @param array<string, mixed> $extraParams
     * @return Notification|null
     */
    protected function getCaseNotification(
        HasApprovalStatuses $actionCase,
        ?Closure $using,
        array|Closure $cases,
        array $extraParams = []
    ): ?Notification {
        $params = $this->getEvaluateParametersForCaseNotification($actionCase);
        $params = [
            ...$params,
            ...$extraParams
        ];

        if (!is_null($using)) {
            $notification = $this->evaluate($using, $params);
        } else {
            $caseNotification = $this->evaluate($cases, $params)[$actionCase->value] ?? null;
            $notification = $this->evaluate($caseNotification, $params);
        }

        return $this->resolveNotification($notification);
    }

    /**
     * @param HasApprovalStatuses $actionCase
     * @return array<string, mixed>
     */
    protected function getEvaluateParametersForCaseNotification(HasApprovalStatuses $actionCase): array
    {
        return [
            'status' => $actionCase->value,
            'statusLabel' => $actionCase::getCaseLabel($actionCase),
        ];
    }

    protected function resolveNotification(mixed $notification): ?Notification
    {
        if (is_null($notification) || $notification instanceof Notification) {
            return $notification?->send();
        }

        return Notification::make()
            ->title($notification)
            ->success();
    }
}
