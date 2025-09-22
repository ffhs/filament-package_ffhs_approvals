<?php

namespace Ffhs\Approvals\Filament\Actions;

use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Traits\Filament\HasApprovalFlowFromRecord;
use Ffhs\Approvals\Traits\Filament\HasApprovalKey;
use Ffhs\Approvals\Traits\Filament\HasApprovalNotification;
use Ffhs\Approvals\Traits\Filament\HasApprovalSingleStateAction;
use Ffhs\Approvals\Traits\Filament\HasGroupLabels;
use Ffhs\Approvals\Traits\Filament\HasRecordUsing;
use Ffhs\Approvals\Traits\Filament\HasResetApprovalAction;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
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
    use HasRecordUsing;

    //use HasColumns; //ToDo implement

    protected bool|Closure $isFullWidth = false;
    protected string $view = 'filament-package_ffhs_approvals::filament.approval-actions';
    protected bool|Closure $requiresConfirmation = false;

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

    public static function make(string|Closure $approvalKey): static
    {
        $static = app(static::class, ['approvalKey' => $approvalKey]);
        $static->configure();

        return $static;
    }

    public function getRecord(): ?Model
    {
        return $this->getRecordFromUsing();
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

    public function getDefaultChildSchemas(): array
    {
        $schemas = [];

        foreach ($this->getApprovalFlow()->getApprovalBys() as $approvalBy) {
            $schemas[$approvalBy->getName()] = $this->makeChildSchema($approvalBy->getName())
                ->components($this->getApprovalByActions($approvalBy))
                ->record($this->getRecordFromUsing())
                ->parentComponent($this);
        }

        return $schemas;
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
        return $this->getApprovalFlow()->isDisabled();
    }
}
