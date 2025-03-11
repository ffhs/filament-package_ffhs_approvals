<?php

namespace Ffhs\Approvals\Concerns;

use App\Models\User;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Models\Approval;
use Ffhs\Approvals\Traits\HasApprovalKey;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Event\InvalidArgumentException;
use PHPUnit\Event\RuntimeException;

trait HandlesApprovals
{
    use HasApprovalKey;

    private ?ApprovalFlow $approvalFlow = null;
    private ?Collection $cachedApprovals = null;
    private ApprovalBy $approvalBy;


    public function canApprove(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->getApprovalBy()->canApprove($user, $this->approvable());
    }

    public function getApprovalBy(): ApprovalBy
    {
        return $this->approvalBy;
    }

    public function approvable(): Approvable|Model
    {
        return $this->getRecord();
    }

    public function approvalFlow(ApprovalFlow $approvalFlow): static
    {
        $this->approvalFlow = $approvalFlow;

        return $this;
    }

    public function approvalBy(ApprovalBy $approvalBy): static
    {
        $this->approvalBy = $approvalBy;

        return $this;
    }

    public function hasCurrentApprovalStatus(): bool
    {
        return $this->getBoundApprovals()->count() > 0;
    }

    public function getBoundApprovals(): ?Collection
    {
        if (!$this->cachedApprovals) {
            $this->cachedApprovals = $this
                ->approvable()
                ->approvals
                ->where(fn(Approval $approval) => $approval->key == $this->getApprovalKey()
                    && $approval->approval_by == $this->getApprovalBy()->getName()
                );
        }

        return $this->cachedApprovals;
    }

    public function getApprovedStatusColor(): string
    {
        return $this->statusCategoryColors['approved'] ?? 'green';
    }

    public function getDeniedStatusColor(): string
    {
        return $this->statusCategoryColors['denied'] ?? 'red';
    }

    public function getPendingStatusColor(): string
    {
        return $this->statusCategoryColors['pending'] ?? 'blue';
    }

    public function statusCategoryColors(array $colors): static
    {
        $allowedKeys = ['approved', 'denied', 'pending'];
        $validColors = array_keys(FilamentColor::getColors());

        foreach ($colors as $key => $value) {
            if (!in_array($key, $allowedKeys, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid status key: %s. Allowed keys are: %s',
                        $key,
                        implode(', ', $allowedKeys)
                    )
                );
            }

            if (!in_array($value, $validColors, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid color value for \'%s\': %s. Allowed colors are:: %s',
                        $key,
                        $value,
                        implode(', ', $validColors)
                    )
                );
            }

            $this->statusCategoryColors[$key] = $value;
        }

        return $this;
    }

    public function getApprovalFlow(): ?ApprovalFlow
    {
        if ($this->approvalFlow) {
            return $this->approvalFlow;
        }

        throw new RuntimeException('No approval flow was found for component'); //todo find right exeption
    }

    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'approvals' => $this->getBoundApprovals(),
            'approvalFlow' => $this->getApprovalFlow(),
            'approvalBy' => $this->getApprovalBy(),
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
