<?php

namespace Ffhs\Approvals;

use Closure;
use Ffhs\Approvals\Models\Approval;
use Filament\Support\Concerns\EvaluatesClosures;

class ApprovalFlow
{
    use EvaluatesClosures;

    protected bool | Closure $isChained = false;

    protected int $atLeast = 1;

    protected array $approvalBy = [];

    protected ?string $name = null;

    final public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function isChained(): bool
    {

        return $this->evaluate($this->isChained);
    }

    public function chained(bool | Closure $condition = true): static
    {

        $this->isChained = $condition;

        return $this;
    }

    public function atLeast(int $atLeast): static
    {
        $this->atLeast = $atLeast;

        return $this;
    }

    /**
     * Set the approvals required.
     *
     * @param  ApprovalBy[]  $approvalBy  An array of ApprovalBy objects.
     */
    public function approvalBy(array $approvalBy): static
    {
        foreach ($approvalBy as $item) {
            if (! $item instanceof ApprovalBy) {
                throw new \InvalidArgumentException('Each element must be an instance of ApprovalBy.');
            }
        }

        $this->approvalBy = $approvalBy;

        return $this;
    }

    public function shouldDisable($record, $category, $scope, $statusClass): bool
    {
        // Assumes downstream dev is using spatie's permissions package

        $user = auth()->user();

        if (! $this->isChained()) {

            $conditions = [];

            foreach ($this->approvalBy as $approvalStep) {

                if (in_array(true, $conditions)) {
                    return true;
                }

                if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {
                    $conditions[] = false;

                    continue;
                }

                if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {
                    $conditions[] = ! $user->hasRole($approvalStep->getRole());

                    continue;
                }

                if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {
                    $conditions[] = ! $user->hasPermissionTo($approvalStep->getPermission());

                    continue;
                }
            }

            return in_array(true, $conditions);

        }

        $currentStep = $this->currentStep($record, $category, $scope, $statusClass);

        $approvalStep = $currentStep !== null ? $this->approvalBy[$currentStep] : null;

        if (! $approvalStep) {
            return false;
        }

        if ($currentStep > 0 && Approval::userHasApproved($record, $statusClass, $category, $scope)) {
            return false;
        }

        if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {
            return false;
        }

        if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {
            return ! $user->hasRole($approvalStep->getRole());
        }

        if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {
            return ! $user->hasPermissionTo($approvalStep->getPermission());
        }

        return false;
    }

    public function currentStep($record, $category, $scope, $statusClass): ?int
    {
        foreach ($this->approvalBy as $index => $approvalStep) {
            if (! $approvalStep->isSatisfied($record, $category, $scope, $statusClass)) {
                return $index;
            }
        }

        return null;
    }

    public function shouldBeVisible(): bool
    {
        // Assumes downstream dev is using spatie's permissions package

        $user = auth()->user();
        $conditions = [];

        foreach ($this->approvalBy as $approvalStep) {

            if (in_array(true, $conditions)) {
                return true;
            }

            if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {

                $conditions[] = true;

                continue;
            }

            if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {

                $conditions[] = $user->hasRole($approvalStep->getRole());

                continue;
            }

            if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {

                $conditions[] = $user->hasPermissionTo($approvalStep->getPermission());

                continue;
            }
        }

        return in_array(true, $conditions);
    }
}
