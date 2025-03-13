<?php

namespace Ffhs\Approvals\Approval;

use Error;
use Exception;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\ApprovalBy;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Contracts\Approver;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\Approval\CanBeAny;
use Ffhs\Approvals\Traits\Approval\HasApproveUsing;
use Ffhs\Approvals\Traits\Approval\HasAtLeast;
use Ffhs\Approvals\Traits\Approval\HasPermissions;
use Ffhs\Approvals\Traits\Approval\HasRoles;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class SimpleApprovalBy implements ApprovalBy
{
    use HasPermissions;
    use EvaluatesClosures;
    use CanBeAny;
    use HasRoles;
    use CanBeAny;
    use HasAtLeast;
    use HasApproveUsing;

    protected ?string $name = null;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function canApprove(Approver|Model $approver, Approvable $approvable): bool
    {
        $canApproveUsing = $this->getCanApproveUsing();
        if ($canApproveUsing) {
            return $this->evaluate($canApproveUsing, [
                'approver' => $approver,
                'approvable' => $approvable,
            ]);
        }

        if ($this->isAny()) {
            return true;
        }

        if ($approver instanceof User) {
            return Gate::allows('can_approve_by', $this);
        }

        return $this->canApproveFromPermissions($approver);
    }

    public function canApproveFromPermissions(Approver|Model $approver): bool
    {
        try {
            if ($this->getRole()) {
                return $approver->hasRole($this->getRole());
            }

            if ($this->getPermission()) {
                /** @var Role $approver */
                return $approver->hasPermissionTo($this->getPermission());
            }
        } catch (Error|Exception) {
        }

        return false;
    }

    public function approved(Model|Approvable $approvable, string $key): ApprovalState
    {
        $approvals = $this->getApprovals($approvable, $key);
        $flow = $this->getApprovalFlow($approvable, $key);
        /** @var HasApprovalStatuses $statusClass */
        $statusClass = $flow?->getStatusEnumClass();

        $deniedStatuses = collect($statusClass::getDeniedStatuses())->map(fn($status) => $status->value);
        $denied = $approvals
            ->whereIn('status', $deniedStatuses)
            ->isNotEmpty();

        if ($denied) {
            return ApprovalState::DENIED;
        }

        $pendingStatuses = collect($statusClass::getPendingStatuses())->map(fn($status) => $status->value);
        $pending = $approvals
            ->whereIn('status', $pendingStatuses)
            ->isNotEmpty();

        if ($pending) {
            return ApprovalState::PENDING;
        }

        if (!$this->reachAtLeast($approvable, $key)) {
            return ApprovalState::OPEN;
        }

        $approvedStatuses = collect($statusClass::getApprovedStatuses())->map(fn($status) => $status->value);
        $open = $approvals
            ->whereNotIn('status', $approvedStatuses)
            ->isNotEmpty();

        if ($open) {
            return ApprovalState::OPEN;
        }

        return ApprovalState::APPROVED;
    }

    public function getApprovals(Model|Approvable $approvable, $key): Collection
    {
        return $approvable
            ->approvals
            ->where(fn(Approval $approval) => $approval->key === $key && $approval->approval_by === $this->getName());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getApprovalFlow(Model|Approvable $approvable, string $key): ?ApprovalFlow
    {
        return $approvable->getApprovalFlow($key);
    }

    public function reachAtLeast(Approvable|Model $approvable, $key): bool
    {
        $approvals = $this->getApprovals($approvable, $key);

        return $approvals->count() >= $this->getAtLeast();
    }
}
