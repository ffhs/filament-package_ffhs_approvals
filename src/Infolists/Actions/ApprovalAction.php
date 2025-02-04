<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\ApprovalBy;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Infolists\Components\Actions\Action;

class ApprovalAction extends Action
{
    use HandlesApprovals;

    protected ?HasApprovalStatuses $status = null;
    protected ApprovalBy $approvalBy;

    public function approvalBy(ApprovalBy $approvalBy):static
    {
        $this->approvalBy = $approvalBy;
        return $this;
    }



    public function status(HasApprovalStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?HasApprovalStatuses
    {
        return $this->status;
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name);
        $this->modalDescription(function () {
                if ($this->actionHasCurrentApprovalStatus()) {
                    return "Are you sure you want to unmark this as $this->name?"; //toDo Translation
                }
                return "Are you sure you want to mark this as $this->name?";
            });
        $this->action($this->process(...));
    }


    public function process(){

    }



}
