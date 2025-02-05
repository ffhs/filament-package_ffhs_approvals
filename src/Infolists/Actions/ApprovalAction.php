<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Infolists\Components\Actions\Action;

class ApprovalAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;

    protected ?HasApprovalStatuses $status = null;
    protected ApprovalBy $approvalBy;
    private string|array|null $colorSelected = null;
    private string|array|null $colorNotSelected = null;
    private string|\Closure $approvalKey;

    protected array $statusCategoryColors = [
        'approved' => 'success',
        'declined' => 'danger',
        'pending' => 'info',
    ];


    public function status(HasApprovalStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): ?HasApprovalStatuses
    {
        return $this->status;
    }





    public function colorSelected(null|string|array $colorSelected):static
    {
        $this->colorSelected = $colorSelected;
        return $this;
    }

    public function colorNotSelected(null|string|array $colorNotSelected):static
    {
        $this->colorNotSelected = $colorNotSelected;
        return $this;
    }

    public function getColorSelected(): null|string|array
    {
        return $this->evaluate($this->colorSelected);
    }

    public function getColorNotSelected(): string|array|null
    {
        return $this->evaluate($this->colorNotSelected);
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
        //ToDo
    }

    public function isDisabled(): bool
    {
        if($this->evaluate($this->isDisabled) || $this->isHidden()) return true;
        return !$this->canApprove();
    }


    public function getColor(): string | array | null
    {
        if (! $this->actionHasCurrentApprovalStatus()) {
            $colors = $this->getColorNotSelected();
            if(!is_array($colors)) return $colors ?? 'gray';
            return $colors[$this->getStatus()->value] ?? 'gray';
        }

        $state = $this->getStatus();

        $statusEnum = $this->getStatusEnumClass();
        $colors = $this->getColorSelected();

        if(!is_null($colors)){
            if(!is_array($colors)){
                return $colors;
            }
            if(array_key_exists($state->value, $colors)) {
                return $colors[$state->value];
            }
        }


        if (in_array($state, $statusEnum::getApprovedStatuses(), true)) {
            return $this->getApprovedStatusColor();
        }

        if (in_array($state, $statusEnum::getDeclinedStatuses(), true)) {
            return $this->getDeclinedStatusColor();
        }

        if (in_array($state, $statusEnum::getPendingStatuses(), true)) {
            return $this->getPendingStatusColor();
        }

        return parent::getColor();
    }



}
