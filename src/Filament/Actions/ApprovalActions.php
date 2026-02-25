<?php

namespace Ffhs\Approvals\Filament\Actions;

use Closure;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\FfhsApprovals;
use Ffhs\Approvals\Traits\Filament\HasApprovalByLabels;
use Ffhs\Approvals\Traits\Filament\HasApprovalFlowFromRecord;
use Ffhs\Approvals\Traits\Filament\HasApprovalKey;
use Ffhs\Approvals\Traits\Filament\HasApprovalSingleStateAction;
use Ffhs\Approvals\Traits\Filament\HasRecordUsing;
use Ffhs\Approvals\Traits\Filament\HasResetApprovalAction;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanRequireConfirmation;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Group;
use Filament\Support\Concerns\HasAlignment;
use Illuminate\Database\Eloquent\Model;

class ApprovalActions extends Component
{
    use HasApprovalSingleStateAction;
    use CanRequireConfirmation;
    use EntanglesStateWithSingularRelationship;
    use HasAlignment;
    use HasApprovalByLabels;
    use HasApprovalKey;
    use HasApprovalFlowFromRecord;
    use HasResetApprovalAction;
    use HasRecordUsing;

    protected string $view = 'filament-package_ffhs_approvals::filament.approval-actions';

    protected bool|Closure $isFullWidth = false;
    protected bool|Closure $requiresConfirmation = false;

    final public function __construct(string|Closure $approvalKey)
    {
        $this->approvalKey = $approvalKey;
    }

    public function isVisible(): bool
    {
        if ($this->getApprovalFlow()->isDisabled()) {
            return false;
        }

        return parent::isVisible();
    }

    public function isFullWidth(): bool
    {
        return (bool)$this->evaluate($this->isFullWidth);
    }

    /**
     * @return Component[]
     */
    protected function getActionsComponents(): array
    {
        $actions = collect($this->getApprovalFlow()->getApprovalBys())
            ->map(function (ApprovalBy $approvalBy) {
                return Group::make()
                    /** @phpstan-ignore-next-line */
                    ->model($this->getRecord())
                    ->statePath($approvalBy->getName())
                    ->columnSpanFull()
                    ->columns(fn() => $this->hasInlineLabel() ? 2 : 1)
                    ->schema([
                        TextEntry::make('title')
                            ->state($this->getApprovalByLabel($approvalBy))
                            ->alignment($this->getAlignment(...))
                            ->hiddenLabel(),
                        Actions::make([])
                            ->fullWidth($this->isFullWidth(...))
                            ->alignment($this->getAlignment(...))
                            ->actions($this->getApprovalByActions($approvalBy))
                    ]);
            });

        return [...$actions];
    }

    public static function make(string|Closure $approvalKey): static
    {
        $static = app(static::class, ['approvalKey' => $approvalKey]);
        $static->configure();

        return $static;
    }

    public function getRecord(bool $withContainerRecord = true): ?Model
    {
        return $this->getRecordFromUsing() ?? parent::getRecord($withContainerRecord);
    }

    public function fullWidth(bool|Closure $isFullWidth = true): static
    {
        $this->isFullWidth = $isFullWidth;

        return $this;
    }

    /**
     * @param ApprovalBy $approvalBy
     * @return Action[]
     */
    public function getApprovalByActions(ApprovalBy $approvalBy): array
    {
        $actions = [];

        foreach ($this->getApprovalStatuses() as $status) {
            $actions[] = $this
                ->getApprovalSingleStateAction($approvalBy, $status);
        }

        $actions[] = $this->getResetApprovalAction($approvalBy);

        return $actions;
    }

    protected function setUp(): void
    {
        $this->statePath($this->getApprovalKey());
        $this->schema($this->getActionsComponents(...));

        $this->notificationOnResetApproval(
            fn(ApprovalBy $approvalBy) => FfhsApprovals::__(
                'approval_actions.notifications.reset_approval',
                ['approval_by' => $approvalBy->getName()]
            )
        );

        $this->casesNotificationOnRemoveApproval(
            fn(string $status, string $statusLabel) => [
                $status => FfhsApprovals::__(
                    'approval_actions.notifications.remove_approval',
                    [
                        'status' => $statusLabel,
                        'status_label' => $statusLabel,
                    ]
                )
            ]
        );

        $this->casesNotificationOnChangeApproval(
            fn(string $status, string $statusLabel, string $lastStatus, string $lastStatusLabel) => [
                $status => FfhsApprovals::__(
                    'approval_actions.notifications.update_approval_to',
                    [
                        'status' => $statusLabel,
                        'status_label' => $statusLabel,
                        'last_status' => $lastStatus,
                        'last_status_label' => $lastStatusLabel
                    ]
                )
            ]
        );

        $this->casesNotificationOnSetApproval(
            fn(string $status, string $statusLabel) => [
                $status => FfhsApprovals::__(
                    'approval_actions.notifications.set_approval_to',
                    ['status' => $statusLabel, 'status_label' => $statusLabel]
                )
            ]
        );
    }

}
