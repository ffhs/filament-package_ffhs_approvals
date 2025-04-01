<?php

namespace Ffhs\Approvals\Infolists\Actions;

use BackedEnum;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Filament\HasApprovalNotification;
use Filament\Infolists\Components\Actions\Action;
use UnitEnum;

class ApprovalByResetAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;
    use HasApprovalNotification;

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
            ->first(); //ToDo only Person or Premission
    }

    public function resetByApproval(): void
    {
        $lastStatus = $this->getActionApproval();
        $lastStatus?->delete();
        $status = $lastStatus->status;

        if ($status instanceof UnitEnum) {
            /** @var BackedEnum $status */
            $status = $status->value;
        }

        $this->sendNotificationOnResetApproval($status);

        $this
            ->getRecord()
            ->refresh();
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
}
