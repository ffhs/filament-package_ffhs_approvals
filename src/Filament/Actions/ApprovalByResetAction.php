<?php

namespace Ffhs\Approvals\Filament\Actions;

use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Filament\HasApprovalNotification;
use Ffhs\Approvals\Traits\Filament\HasRecordUsing;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ApprovalByResetAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;
    use HasApprovalNotification;
    use HasRecordUsing;

    public function isDisabled(): bool
    {
        return ($this->evaluate($this->isDisabled) || $this->isHidden()) || !$this->canApprove();
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }

        $approval = $this->getActionApproval();

        return is_null($approval);
    }

    public function getActionApproval(): ?Approval
    {
        return $this
            ->getApprovalBy()
            ->getApprovals($this->getRecord(), $this->getApprovalKey())
            ->first();
    }

    public function getRecord(bool $withDefault = true): ?Model
    {
        return $this->getRecordFromUsing();
    }

    public function resetByApproval(): void
    {
        $lastStatus = $this->getActionApproval();
        $status = $lastStatus?->status;
        $lastStatus?->delete();

        $this->sendNotificationOnResetApproval($status);

        $this->getRecord()
            ?->refresh();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-m-arrow-uturn-left')
            ->tooltip('Status Zurücksetzen')
            ->color('gray')
            ->label('')
            ->action($this->resetByApproval(...));
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'approvals' => [$this->getBoundApprovals()],
            'approvalFlow' => [$this->getApprovalFlow()],
            'approvalBy' => [$this->getApprovalBy()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
