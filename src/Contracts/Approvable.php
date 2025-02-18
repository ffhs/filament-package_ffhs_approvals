<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Enums\ApprovalState;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property Collection approvals
 */
interface Approvable
{

    public function approvals(): MorphMany;

    /**
     * @return array<string, ApprovalFlow>
     */
    function getApprovalFlows(): array;

    public function getApprovalFlow(string $key): ?ApprovalFlow;


    public function approvalStatistics(?array $categories = null, ?array $keys = null): array;

    public function getFilteredApprovalFlow(?array $categories = null, ?array $keys = null): array;


    public function approved(?array $categories = null, ?array $keys = null): ApprovalState;

    public function isApproved(?array $categories = null, ?array $keys = null): bool;

    public function isDenied(?array $categories = null, ?array $keys = null): bool;

    public function isPending(?array $categories = null, ?array $keys = null): bool;

    public function isOpen(?array $categories = null, ?array $keys = null): bool;

}
