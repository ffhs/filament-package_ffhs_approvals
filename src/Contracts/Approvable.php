<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Approval> $approvals
 * @property int $id
 */
interface Approvable
{
    /**
     * @return MorphMany<Approval, Model>
     */
    public function approvals(): MorphMany;

    /**
     * @return array<string, ApprovalFlow>
     */
    public function getApprovalFlows(): array;

    public function getApprovalFlow(string $key): ?ApprovalFlow;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return array<string, mixed>
     */
    public function approvalStatistics(?array $categories = null, ?array $keys = null): array;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return array<string, ApprovalFlow>
     */
    public function getFilteredApprovalFlow(?array $categories = null, ?array $keys = null): array;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return ApprovalState
     */
    public function approved(?array $categories = null, ?array $keys = null): ApprovalState;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return bool
     */
    public function isApproved(?array $categories = null, ?array $keys = null): bool;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return bool
     */
    public function isDenied(?array $categories = null, ?array $keys = null): bool;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return bool
     */
    public function isPending(?array $categories = null, ?array $keys = null): bool;

    /**
     * @param string[]|null $categories
     * @param string[]|null $keys
     * @return bool
     */
    public function isOpen(?array $categories = null, ?array $keys = null): bool;
}
