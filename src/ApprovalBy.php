<?php

namespace Ffhs\Approvals;

use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;

class ApprovalBy
{
    protected ?string $name = null;

    protected ?string $role = null;

    protected ?string $permission = null;

    protected int $atLeast = 1;

    protected bool $satisfied = false;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function role(string $role): static
    {
        $this->role = $role;

        return $this;
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

    public function getAccess(): ?string
    {
        // Assuming role should take precedence over a permission

        if ($this->role) {
            return 'role:' . $this->role;
        }

        if ($this->permission) {
            return 'permission:' . $this->permission;
        }

        return null;
    }

    public function isSatisfied(Model $record, $category, $statusClass): bool
    {

        $approvals = Approval::where('category', $category)
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->whereIn('status', $statusClass::getApprovedStatuses())
            ->whereJsonContains('config->access', $this->getAccess())
            ->get();

        $conditions = [];

        foreach ($approvals as $approval) {

            $approver = $approval->approver;

            if ($this->role === null && $this->permission === null) {
                $conditions[] = true;
            }

            if ($this->role && method_exists($approver, 'hasRole')) {
                $conditions[] = $approver->hasRole($this->role);
            }

            if ($this->permission && method_exists($approver, 'hasPermissionTo')) {
                $conditions[] = $approver->hasPermissionTo($this->permission);
            }
        }

        $countTrue = count(array_filter($conditions));

        $this->satisfied = $countTrue >= $this->atLeast;

        return $this->satisfied;
    }

    public function satisfied(bool $satisfied = true): static
    {
        $this->satisfied = $satisfied;

        return $this;
    }
}
