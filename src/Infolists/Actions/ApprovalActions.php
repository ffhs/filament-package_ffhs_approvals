<?php

namespace Ffhs\Approvals\Infolists\Actions;

use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Traits\HasApprovalFlowFromRecord;
use Ffhs\Approvals\Traits\HasApprovalKey;
use Ffhs\Approvals\Traits\HasApprovalNotification;
use Ffhs\Approvals\Traits\HasApprovalSingleStateAction;
use Ffhs\Approvals\Traits\HasGroupLabels;
use Ffhs\Approvals\Traits\HasResetApprovalAction;
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
    use HasGroupLabels;
    use HasApprovalKey;
    use HasApprovalFlowFromRecord;
    use EntanglesStateWithSingularRelationship;
    use HasApprovalNotification;
    use HasApprovalSingleStateAction;
    use HasResetApprovalAction;

    //use HasColumns; //ToDo implement

    protected bool|Closure $isFullWidth = false;
    protected string $view = 'filament-package_ffhs_approvals::infolist.approval-actions';
    protected bool|Closure $requiresConfirmation = false;
    private ?Model $record = null;

    final public function __construct(string|Closure $approvalKey)
    {
        $this->notificationOnResetApproval(
            fn(ApprovalSingleStateAction $action) => 'Approval is reset ' . $action->getApprovalBy()->getName()
        );
        $this->notificationOnChangeApproval(
            fn($lastStatus, $status) => 'change approval from ' . $lastStatus . ' to ' . $status
        );
        $this->notificationOnSetApproval(
            fn($status) => 'set approval to ' . $status
        );
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
        if (is_null($this->record)) {
            return parent::getRecord();
        }

        return $this->evaluate($this->record, ['record' => parent::getRecord()]);
    }

    public function fullWidth(bool|Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    public function isFullWidth(): bool
    {
        return (bool)$this->evaluate($this->isFullWidth);
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

    public static function make(string|Closure $approvalKey): static
    {
        $static = app(static::class, ['approvalKey' => $approvalKey]);
        $static->configure();

        return $static;
    }

    public function getApprovalByActions(ApprovalBy $approvalBy): array
    {
        $actions = [];

        foreach ($this->getApprovalStatuses() as $status) {
            $actions[] = $this
                ->getApprovalSingleStateAction($approvalBy, $status)
                ->toInfolistComponent();
        }

        $actions[] = $this
            ->getResetApprovalAction($approvalBy)
            ->toInfolistComponent();

        return $actions;
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

    public function hasChildComponentContainer(bool $withHidden = false): bool
    {
        if (!$withHidden && $this->isHidden()) {
            return false;
        }

        return sizeof($this->getApprovalFlow()->getApprovalBys()) > 0;
    }

    public function isHidden(): bool
    {
        if (parent::isHidden()) {
            return true;
        }
        return $this->getApprovalFlow()->isApprovalDisabled();
    }
}
