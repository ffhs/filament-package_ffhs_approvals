<?php

namespace Ffhs\Approvals\Infolists\Actions;

use App\Models\User;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ApprovalSingleStateAction extends Action implements ApprovableByComponent
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
    private array $approvalIcons = [];


    public function changeApproval():void{
        if($this->isActionActive()){
            $this->getActionBoundApproval()->delete();
            Notification::make()
                ->title('remove approval') //ToDo Translate and add text state
                ->success()
                ->send();
            return;
        }

        $isChange = !is_null($this->getStatus());
        $oldState = $this->getStatus();

        if($isChange) $this->getActionBoundApproval()->delete();

        Approval::create([
            'approver_id' => Auth::id(),
            'approver_type' => User::class,
            'approvable_id' => $this->approvable()->id,
            'approvable_type' => $this->approvable()::class,
            'status' => $this->getActionStatus()->value,
            'key' => $this->getApprovalKey(),
            'approval_by' => $this->getApprovalBy()->getName()
        ]);

        if($isChange){
            Notification::make()
                ->title('change approval from ' . $oldState->value . ' to ' . $this->getActionStatus()->value) //ToDo Translate and add text state
                ->success()
                ->send();
        }
        else{
            Notification::make()
                ->title('set approval to ' . $this->getActionStatus()->value) //ToDo Translate and add text state
                ->success()
                ->send();
        }

        dd($this->approvable()->approvalStatistics());


    }


    public function actionStatus(HasApprovalStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * The Sate which the Action is bound, as example the 'approve' because it's the approval action
     * @return HasApprovalStatuses|null
     */
    public function getActionStatus(): ?HasApprovalStatuses
    {
        return $this->status;
    }

    public function isActionActive(): bool
    {
       return $this->getStatus() === $this->getActionStatus();
    }

    public function getStatus(): ?\UnitEnum
    {
        return $this->getActionBoundApproval()?->status;
    }


    public function getActionBoundApproval(): ?Approval
    {
        return $this->getBoundApprovals()->first();
//            ->firstWhere(function (Approval $approval) {
//                return $approval->approver_id == Auth::id() &&
//                    $approval->approver_type == User::class;
//            }); //ToDo Later for at least
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

    public function approvalIcons(array $approvalIcons): static
    {
        $this->approvalIcons = $approvalIcons;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->name($this->name);
        $this->action($this->changeApproval(...));
        $this->iconPosition(IconPosition::After);
    }

    public function getIcon(): string|Htmlable|null
    {
        $this->icon = $this->approvalIcons[$this->getActionStatus()->value] ?? null;
        return parent::getIcon();
    }


    public function isDisabled(): bool
    {
        if($this->evaluate($this->isDisabled) || $this->isHidden()) return true;
        return !$this->canApprove();
    }


    public function getColor(): string | array | null
    {
        if (! $this->isActionActive()) {
            $colors = $this->getColorNotSelected();
            if(!is_array($colors)) return $colors ?? 'gray';
            return $colors[$this->getActionStatus()->value] ?? 'gray';
        }

        $state = $this->getActionStatus();

        $statusEnum = $this->getApprovalFlow()->getStatusEnumClass();
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
