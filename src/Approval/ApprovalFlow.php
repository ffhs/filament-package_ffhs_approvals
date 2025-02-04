<?php

namespace Ffhs\Approvals;

use Closure;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Database\Eloquent\Model;
use LaraDumpsCore\___PHPSTORM_HELPERS\static;

class ApprovalFlow
{
    use EvaluatesClosures;
    private Model|Closure|null $record;

    protected array|Closure $approvalBy = [];
    protected string|Closure $category;
    protected array|Closure $approvalStatus;

    public static function make():static
    {
        return app(static::class);
    }

    public function approvableRecord(Model|Closure|null $record): static
    {
        $this->record = $record;
        return $this;
    }

    public function getApprovalRecord(): Model
    {
        return $this->evaluate($this->record);
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'record' => ['record' => $this->getApprovalRecord()]
        };
    }

    public function approvalBy(array|Closure $approvalBy): static
    {
        $this->approvalBy = $approvalBy;
        return $this;
    }

    public function getApprovalBy(): array{
        return $this->evaluate($this->approvalBy);
    }

    public function category(string|Closure $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory(): string{
        return $this->evaluate($this->category);
    }


    public function approvalStatus(array|Closure $approvalStatus): static
    {
        $this->approvalStatus = $approvalStatus;
        return $this;
    }

    public function getApprovalStatus(): array{
        return $this->evaluate($this->approvalStatus);
    }






//    use EvaluatesClosures;
//
//    protected bool | Closure $isChained = false;
//
//    protected array $approvalBy = [];
//
//    protected ?string $name = null;
//
//    protected array $steps = [];
//
//    final public function __construct(string $name)
//    {
//        $this->name = $name;
//    }
//
//    public static function make(string $name): static
//    {
//        return new static($name);
//    }
//
//    public function isChained(): bool
//    {
//
//        return $this->evaluate($this->isChained);
//    }
//
//    public function chained(bool | Closure $condition = true): static
//    {
//
//        $this->isChained = $condition;
//
//        return $this;
//    }
//
//    /**
//     * Set the approvals required.
//     *
//     * @param  ApprovalBy[]  $approvalBy  An array of ApprovalBy objects.
//     */
//    public function approvalBy(array $approvalBy): static
//    {
//        $approvalsNeeded = 0;
//        foreach ($approvalBy as $index => $item) {
//            if (! $item instanceof ApprovalBy) {
//                throw new InvalidArgumentException('Each element must be an instance of ApprovalBy.');
//            }
//
//            for ($i = 0; $i < $item->getAtLeast(); $i++) {
//
//                $approvalsNeeded++;
//
//                $step = [
//                    'approvalByIndex' => $index,
//                    'config' => [
//                        'order' => $this->isChained() ? $approvalsNeeded : null,
//                        'access' => $item->getAccess(),
//                    ],
//
//                ];
//                $this->steps[] = $step;
//            }
//        }
//
//        $this->approvalBy = $approvalBy;
//
//        return $this;
//    }
//
//    public function getSteps(): array
//    {
//        return $this->steps;
//    }
//
//    public function bestMatchStep(Model $record, string $category, string $statusClass): array
//    {
//        // all early returns should be the same as the currentStep method
//        $defaultStep = $this->currentStep($record, $category, $statusClass);
//
//        if ($this->isChained()) {
//            return $defaultStep;
//        }
//
//        $stepsWithAccess = array_filter($this->steps, function ($step) {
//            return $step['config']['access'] !== null;
//        });
//
//        if (empty($stepsWithAccess)) {
//            return $defaultStep;
//        }
//
//        $user = auth()->user();
//
//        foreach ($stepsWithAccess as $step) {
//
//            $approvalStep = $this->approvalBy[$step['approvalByIndex']];
//
//            if ($approvalStep->isSatisfied($record, $category, $statusClass)) {
//                continue;
//            }
//
//            $access = explode(':', $step['config']['access']);
//            $type = $access[0];
//            $value = $access[1];
//
//            if ($type == 'role' && method_exists($user, 'hasRole')) {
//                if ($user->hasRole($value)) {
//                    return $step;
//                }
//            }
//
//            if ($type == 'permission' && method_exists($user, 'hasPermissionTo')) {
//                if ($user->hasPermissionTo($value)) {
//                    return $step;
//                }
//            }
//        }
//
//        return $defaultStep;
//    }
//
//    public function shouldDisable($record, $category, $statusClass): bool
//    {
//        // Assumes downstream dev is using spatie's permissions package
//
//        $user = auth()->user();
//
//        if (! $this->isChained()) {
//
//            $conditions = [];
//
//            foreach ($this->approvalBy as $approvalStep) {
//
//                if (in_array(true, $conditions)) {
//                    return true;
//                }
//
//                if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {
//                    $conditions[] = false;
//
//                    continue;
//                }
//
//                if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {
//                    $conditions[] = ! $user->hasRole($approvalStep->getRole());
//
//                    continue;
//                }
//
//                if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {
//                    $conditions[] = ! $user->hasPermissionTo($approvalStep->getPermission());
//
//                    continue;
//                }
//            }
//
//            return in_array(true, $conditions) && ! in_array(false, $conditions);
//
//        }
//
//        $currentStep = $this->currentStep($record, $category, $statusClass);
//
//        $approvalStep = $currentStep !== null ? $this->approvalBy[$currentStep['approvalByIndex']] : null;
//
//        if (! $approvalStep) {
//            return false;
//        }
//
//        if (Approval::userHasApproved($record, $statusClass, $category)) {
//            return false;
//        }
//
//        if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {
//            return false;
//        }
//
//        if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {
//            return ! $user->hasRole($approvalStep->getRole());
//        }
//
//        if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {
//            return ! $user->hasPermissionTo($approvalStep->getPermission());
//        }
//
//        return false;
//    }
//
//    public function currentStep($record, $category, $statusClass): ?array
//    {
//        foreach ($this->approvalBy as $index => $approvalStep) {
//            if (! $approvalStep->isSatisfied($record, $category, $statusClass)) {
//
//                $relevantSteps = array_filter($this->steps, function ($step) use ($index) {
//                    return $step['approvalByIndex'] == $index;
//                });
//
//                if (! empty($relevantSteps)) {
//                    usort($relevantSteps, function ($a, $b) {
//
//                        $orderA = $a['config']['order'];
//                        $orderB = $b['config']['order'];
//
//                        return $orderA <=> $orderB;
//                    });
//
//                    return $relevantSteps[0];
//                }
//
//                return null;
//            }
//        }
//
//        return null;
//    }
//
//    public function shouldBeVisible(): bool
//    {
//        // Assumes downstream dev is using spatie's permissions package
//
//        $user = auth()->user();
//        $conditions = [];
//
//        foreach ($this->approvalBy as $approvalStep) {
//
//            if (in_array(true, $conditions)) {
//                return true;
//            }
//
//            if ($approvalStep->getPermission() == null && $approvalStep->getRole() == null) {
//
//                $conditions[] = true;
//
//                continue;
//            }
//
//            if ($approvalStep->getRole() && method_exists($user, 'hasRole')) {
//
//                $conditions[] = $user->hasRole($approvalStep->getRole());
//
//                continue;
//            }
//
//            if ($approvalStep->getPermission() && method_exists($user, 'hasPermissionTo')) {
//
//                $conditions[] = $user->hasPermissionTo($approvalStep->getPermission());
//
//                continue;
//            }
//        }
//
//        return in_array(true, $conditions);
//    }

}
