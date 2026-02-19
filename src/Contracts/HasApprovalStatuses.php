<?php

namespace Ffhs\Approvals\Contracts;


use BackedEnum;

interface HasApprovalStatuses extends BackedEnum
{

    /**
     * @return static[]
     */
    public static function getApprovedStatuses(): array;

    /**
     * @return static[]
     */
    public static function getDeniedStatuses(): array;

    /**
     * @return static[]
     */
    public static function getPendingStatuses(): array;


    public static function getCaseLabel(self $case): string;
}
