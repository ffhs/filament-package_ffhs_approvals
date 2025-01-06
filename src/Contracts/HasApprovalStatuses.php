<?php

namespace Ffhs\Approvals\Contracts;

interface HasApprovalStatuses
{
    public function getApprovedStatuses(): array;

    public function getDeclinedStatuses(): array;

    public function getPendingStatuses(): array;
}
