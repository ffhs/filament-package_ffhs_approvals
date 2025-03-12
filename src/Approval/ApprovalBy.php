<?php

namespace Ffhs\Approvals\Approval;

use Closure;
use Enum;
use Error;
use Exception;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\Approver;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use UnitEnum;

class ApprovalBy
{
    use EvaluatesClosures;

    protected ?string $name = null;
    protected ?string $role = null;
    protected Closure|UnitEnum|string|null $permission = null;
    protected int $atLeast = 1;
    protected bool $any = false;
    protected ?Closure $canApproveUsing = null;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function any(bool $any = true): static
    {
        $this->any = $any;

        return $this;
    }

    public function canApproveUsing(Closure $canApproveUsing): static
    {
        $this->canApproveUsing = $canApproveUsing;

        return $this;
    }

    public function role(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function permission(Closure|UnitEnum|string|null $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function atLeast(int $atLeast): static
    {
        $this->atLeast = $atLeast;

        return $this;
    }

    public function getAtLeast(): int
    {
        return $this->atLeast;
    }

    public function canApprove(Approver|Model $approver, Approvable $approvable): bool
    {
        if ($this->canApproveUsing) {
            return $this->evaluate($this->canApproveUsing, [
                'approver' => $approver,
                'approvable' => $approvable,
            ]);
        }

        if ($this->isAny()) {
            return true;
        }

        if ($approver instanceof Authenticatable) {
            return Gate::allows('can_approve_by', $this);
        }

        return $this->canApproveFromPermissions($approver);
    }

    public function isAny(): bool
    {
        return $this->any;
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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getPermission(): ?string
    {
        $permission = $this->evaluate($this->permission);

        if ($permission instanceof UnitEnum) {
            /** @var Enum $permission */
            $permission = $permission->value;
        }

        return $permission;
    }

    public function approved(Model|Approvable $approvable, string $key): ApprovalState
    {
        $approvals = $this->getApprovals($approvable, $key);
        $flow = $this->getApprovalFlow($approvable, $key);
        /** @var HasApprovalStatuses $statusClass */
        $statusClass = $flow->getStatusEnumClass();

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
            ->where(fn(Approval $approval) => $approval->key == $key && $approval->approval_by == $this->getName());
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

        return $approvals->count() >= $this->atLeast;
    }
}
