<?php

namespace Ffhs\Approvals\Contracts;

use Ffhs\Approvals\Approval\ApprovalFlow;
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
    public function getApprovalFlows(): array;




    public function approvalStatistics(?array $categories = null, ?array $keys = null): array;


    public function approved(?array $categories = null, ?array $keys = null, bool $failOnNotReachAtLeast = true): int;
    public function anyDenied(?array $categories = null, ?array $keys = null): bool;

}
