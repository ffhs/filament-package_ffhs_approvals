<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Traits\HasApprovalActionModifications;
use Ffhs\Approvals\Traits\HasApprovalFlowFromRecord;
use Ffhs\Approvals\Traits\HasApprovalKey;
use Ffhs\Approvals\Traits\HasNeedResetApprovalBeforeChange;
use Filament\Actions\Concerns\HasSize;
use Filament\Infolists\ComponentContainer;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasVerticalAlignment;
use Illuminate\Database\Eloquent\Model;
use Mockery\Matcher\Closure;

class ApprovalActions extends Component
{
    use HasAlignment;
    use HasVerticalAlignment;
    use HasApprovalActionModifications;
    use HasNeedResetApprovalBeforeChange;
    use HasApprovalKey;
    use HasApprovalFlowFromRecord;
    use EntanglesStateWithSingularRelationship;
    //use HasColumns; //ToDo implement
    use HasSize;

    private ?Model $record = null;



    protected bool | Closure $isFullWidth = false;
    protected string $view = 'filament-package_ffhs_approvals::infolist.approval-actions';


    protected bool|Closure $requiresConfirmation = false;


    final public function __construct(string|Closure $approvalKey)
    {
        $this->approvalKey($approvalKey);
        $this->statePath($this->getApprovalKey());
    }

    public function recordUsing(Closure|null|Model $record): static
    {
        $this->record = $record;
        return $this;
    }

    public function getRecord(): ?Model
    {
        if(is_null($this->record)) return parent::getRecord();
        return $this->evaluate($this->record, ['record' => parent::getRecord()]);
    }


    public static function make(string|Closure $approvalKey): static
    {
        $static = app(static::class, ['approvalKey' => $approvalKey]);
        $static->configure();

        return $static;
    }


    public function isHidden(): bool
    {
        if(parent::isHidden()) return true;
        return $this->getApprovalFlow()->isApprovalDisabled();
    }


    public function getResetApprovalByAction(ApprovalBy $approvalBy): ApprovalByResetAction
    {
        return ApprovalByResetAction::make($approvalBy->getName() . '-reset_approval')
            ->disabled($this->isApprovalActionsDisabled())
            ->approvalKey($this->getApprovalKey())
            ->approvalBy($approvalBy)
            ->size($this->getSize())
            ->visible(fn() => $this->isNeedResetApprovalBeforeChange());
    }



    public function getApprovalByActions(ApprovalBy $approvalBy):array
    {
        $labelMap = $this->getApprovalActionsLabel();

        $actions = [];
        $toolTips = $this->getApprovalActionToolTips();

        foreach ($this->getApprovalStatuses() as $status){
            $label = $labelMap[$status->value] ?? $status->value;

            $actions[] = ApprovalSingleStateAction::make($approvalBy->getName() . '-' . $status->value)
                ->needResetApprovalBeforeChange($this->isNeedResetApprovalBeforeChange())
                ->approvalFlow($this->getApprovalFlow())
                ->requiresConfirmation($this->isRequiresConfirmation())
                ->colorSelected($this->getApprovalActionsSelectColor())
                ->colorNotSelected($this->getApprovalActionsColor())
                ->approvalIcons($this->getApprovalActionsIcons())
                ->disabled($this->isApprovalActionsDisabled())
                ->approvalKey($this->getApprovalKey())
                ->tooltip($toolTips[$status->value] ?? null)
                ->label($label)
                ->size($this->getSize())
                ->approvalBy($approvalBy)
                ->actionStatus($status)
                ->toInfolistComponent();
        }

        $actions[] = $this->getResetApprovalByAction($approvalBy)->toInfolistComponent();

        return $actions;
    }


    public function fullWidth(bool | Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool) $this->evaluate($this->isFullWidth);
    }


    public function requiresConfirmation(bool|Closure $requiresConfirmation = true): static
    {
        $this->requiresConfirmation = $requiresConfirmation;
        return $this;
    }


    public function isRequiresConfirmation(): bool
    {
       return $this->evaluate($this->requiresConfirmation);
    }




    public function getChildComponentContainers(bool $withHidden = false): array
    {
        $containers = [];
        foreach ($this->getApprovalFlow()->getApprovalBys() as $approvalBy) {
            $containers[$approvalBy->getName()] = ComponentContainer::make($this->getLivewire())
                ->parentComponent($this)
                ->components($this->getApprovalByActions($approvalBy));
        }

        return $containers;
    }

    public function hasChildComponentContainer(bool $withHidden = false): bool
    {
        if(!$withHidden && $this->isHidden()) return false;
        return sizeof($this->getApprovalFlow()->getApprovalBys()) > 0;
    }





}
