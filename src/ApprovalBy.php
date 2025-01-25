<?php

namespace Ffhs\Approvals;

use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;

class ApprovalBy
{
    protected ?string $name = null;

    protected ?string $role = null;

    protected ?string $permission = null;

    protected bool $multiple = false;

    protected bool $satisfied = false;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
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

    public function isSatisfied(Model $record, $category, $scope, $statusClass): bool
    {

        $approvals = Approval::where('category', $category)
            ->where('scope', $scope)
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->whereIn('status', $statusClass::getApprovedStatuses())
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

        $this->satisfied = in_array(true, $conditions);

        return $this->satisfied;
    }

    public function satisfied(bool $satisfied = true): static
    {
        $this->satisfied = $satisfied;

        return $this;
    }
}
