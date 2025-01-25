<?php

namespace Ffhs\Approvals\Contracts;

interface HasApprovalStatuses
{
    public static function getApprovedStatuses(): array;

    public static function getDeclinedStatuses(): array;

    public static function getPendingStatuses(): array;
}
