<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Models\Approval;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;

class ApprovalByResetAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;

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

    public function isHidden(): bool
    {
        if(parent::isHidden()) return true;

        $approval = $this->getActionApproval();
        return is_null($approval);
    }

    public function resetByApproval(): void
    {
        $this->getActionApproval()?->delete();
        Notification::make()
            ->title('Approval is reset ' . $this->getApprovalBy()->getName()) //ToDo translate
            ->success()
            ->send();

        $this->getRecord()->refresh();
    }

    public function getActionApproval(): ?Approval
    {
        return $this->getApprovalBy()
            ->getApprovals($this->getRecord(), $this->getApprovalKey())
            ->first(); //ToDo only Person or Premission
    }


}
