<?php

namespace Ffhs\Approvals\Approval;

use Closure;
use Error;
use Exception;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\Approver;
use Ffhs\Approvals\Models\Approval;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ApprovalBy
{
    use EvaluatesClosures;

    protected ?string $name = null;

    protected ?string $role = null;

    protected ?string $permission = null;

    protected int $atLeast = 1;

    protected bool $any = false;

    protected ?Closure $canApproveUsing = null;

    public function any(bool $any = true): static
    {
        $this->any = $any;
        return $this;
    }
    public function canApproveUsing(Closure $canApproveUsing): static{
        $this->canApproveUsing = $canApproveUsing;
        return $this;
    }

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function role(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function permission(string $permission): static
    {
        $this->permission = $permission;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
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
        if($this->canApproveUsing){
            return $this->evaluate($this->canApproveUsing, [
                'approver' => $approver,
                'approvable' => $approvable
            ]);
        }

        if($this->any) return true;

        if($approver instanceof Authenticatable){
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
                return $approver->hasPermissionTo($this->getPermission());
            }
        }catch (Error|Exception){}
        return false;
    }

    public function reachAtLeast(Approvable|Model $approvable, $key): bool {
        $approvals = $this->getApprovals($approvable, $key);
        return $approvals->count() >= $this->atLeast;
    }


    public function getApprovals(Model|Approvable $approvable,$key): Collection{
        return $approvable->approvals->where(function (Approval $approval) use ($key) {
            if ($approval->key != $key) {
                return false;
            }
            if ($approval->approval_by != $this->getName()) {
                return false;
            }
            return true;
        });
    }
}
