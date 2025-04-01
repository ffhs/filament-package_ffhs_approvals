<?php

namespace Ffhs\Approvals\Contracts;

use BackedEnum;

interface HasApprovalStatuses
{
    /**
     * @return array<BackedEnum>
     */
    public static function getApprovedStatuses(): array;

    /**
     * @return array<BackedEnum>
     */
    public static function getDeniedStatuses(): array;

    /**
     * @return array<BackedEnum>
     */
    public static function getPendingStatuses(): array;
}
