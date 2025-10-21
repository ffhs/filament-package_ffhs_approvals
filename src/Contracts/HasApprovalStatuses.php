<?php

namespace Ffhs\Approvals\Contracts;

use BackedEnum;

interface HasApprovalStatuses
{

    public static function getApprovedStatuses(): array;


    public static function getDeniedStatuses(): array;


    public static function getPendingStatuses(): array;
}
