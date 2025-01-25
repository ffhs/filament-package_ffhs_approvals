<?php

namespace Ffhs\Approvals\Concerns;

use BackedEnum;
use Closure;
use Ffhs\Approvals\ApprovalFlow;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentColor;

trait HandlesApprovals
{
    protected ?string $category = null;

    protected ?string $scope = null;

    protected ?HasApprovalStatuses $status = null;

    protected array $statusCategoryColors = [
        'approved' => 'success',
        'declined' => 'danger',
        'pending' => 'info',
    ];

    protected ?ApprovalFlow $approvalFlow = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name)
            ->requiresConfirmation()
            ->modalDescription(function () {
                if ($this->actionHasCurrentApprovalStatus()) {
                    return "Are you sure you want to unmark this as $this->name?";
                }

                return "Are you sure you want to mark this as $this->name?";
            })
            ->action($this->process());

    }

    public function scope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function category(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
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

    public function process(): Closure
    {
        return function (): void {
            if (Approval::userHasResponded($this->getRecord(), $this->category, $this->scope) && $this->actionHasCurrentApprovalStatus()) {
                $this->resetApproval();

                return;
            }

            $this->setApproval();

        };
    }

    public function setApproval(): void
    {
        $record = $this->getRecord();
        $recordName = strtolower(class_basename($record));
        assert($this->status instanceof BackedEnum);

        Approval::updateOrCreate(
            [
                'category' => $this->category ?? "approvals-$recordName",
                'scope' => $this->scope,
                'approvable_type' => get_class($record),
                'approvable_id' => $record->id ?? null,
                'approver_id' => auth()->id(),
            ],
            [
                'status' => $this->status->value,
                'approved_at' => now(),
            ]
        );

        Notification::make()
            ->title("Successfully marked as $this->name!")
            ->success()
            ->send();
    }

    public function resetApproval(): void
    {
        $record = $this->getRecord();
        $recordName = strtolower(class_basename($record));
        assert($this->status instanceof BackedEnum);

        Approval::where([
            'category' => $this->category ?? "approvals-$recordName",
            'scope' => $this->scope,
            'approvable_type' => get_class($record),
            'approvable_id' => $record->id ?? null,
            'status' => $this->status->value,
            'approver_id' => auth()->id(),
        ])->delete();

        Notification::make()
            ->title("Successfully unmarked as $this->name!")
            ->success()
            ->send();
    }

    public function getStatusEnumClass(): string
    {
        return $this->status::class;
    }

    protected function getApprovedStatusColor(): string
    {
        return $this->statusCategoryColors['approved'];
    }

    protected function getDeclinedStatusColor(): string
    {
        return $this->statusCategoryColors['declined'];
    }

    protected function getPendingStatusColor(): string
    {
        return $this->statusCategoryColors['pending'];
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor(): string | array | null
    {
        if (! $this->actionHasCurrentApprovalStatus()) {
            return 'gray';
        }

        $statusEnum = $this->getStatusEnumClass();

        if (in_array($this->status, $statusEnum::getApprovedStatuses(), true)) {
            return $this->getApprovedStatusColor();
        }

        if (in_array($this->status, $statusEnum::getDeclinedStatuses(), true)) {
            return $this->getDeclinedStatusColor();
        }

        if (in_array($this->status, $statusEnum::getPendingStatuses(), true)) {
            return $this->getPendingStatusColor();
        }

        return parent::getColor();
    }

    public function statusCategoryColors(array $colors): static
    {
        $allowedKeys = ['approved', 'declined', 'pending'];
        $validColors = array_keys(FilamentColor::getColors());

        foreach ($colors as $key => $value) {
            if (! in_array($key, $allowedKeys, true)) {
                throw new \InvalidArgumentException("Invalid status key: {$key}. Allowed keys are: " . implode(', ', $allowedKeys));
            }

            if (! in_array($value, $validColors, true)) {
                throw new \InvalidArgumentException("Invalid color value for '{$key}': {$value}. Allowed colors are: " . implode(', ', $validColors));
            }

            $this->statusCategoryColors[$key] = $value;
        }

        return $this;
    }

    protected function actionHasCurrentApprovalStatus(): bool
    {
        $approval = Approval::getApprovalForAction($this);

        assert($this->status instanceof BackedEnum);

        return $approval && $approval->status === $this->status->value;
    }

    public function approvalFlow(ApprovalFlow $approvalFlow): static
    {
        $this->approvalFlow = $approvalFlow;

        return $this;
    }

    public function isHidden(): bool
    {

        if (parent::isHidden()) {
            return true;
        }

        if (! $this->actionHasCurrentApprovalStatus() && Approval::userHasResponded($this->getRecord(), $this->category, $this->scope)) {
            return true;
        }

        return false;
    }
}
