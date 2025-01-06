<?php

namespace Ffhs\Approvals\Concerns;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Notifications\Notification;

trait HandlesApprovals
{
    protected ?string $category = null;

    protected ?string $scope = null;

    protected ?HasApprovalStatuses $status = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->name($this->name)
            ->requiresConfirmation()
            ->modalDescription("Are you sure you want to mark this as $this->name?")
            ->action($this->process());

    }

    public function scope(string $scope): static
    {
        $this->scope = $scope;

        return $this;
    }

    public function category(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function status(HasApprovalStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function process(): Closure
    {
        return function (): void {
            $record = $this->getRecord();
            $recordName = strtolower(class_basename($record));

            Approval::firstOrCreate([
                'category' => $this->category ?? "approvals-$recordName",
                'scope' => $this->scope,
                'approvable_type' => get_class($record),
                'approvable_id' => $record->id ?? null,
                'status' => $this->status,
                'approver_id' => auth()->id(),
                'approved_at' => now(),
            ]);

            Notification::make()
                ->title("Successfully marked as $this->name!")
                ->success()
                ->send();
        };
    }
}
