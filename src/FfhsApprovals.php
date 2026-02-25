<?php

namespace Ffhs\Approvals;

class FfhsApprovals
{

    /**
     * @param string $key
     * @param array<string,mixed> $replace
     * @return string|array<string,mixed> |null
     */
    public static function __(string $key, array $replace = []): string|array|null
    {
        return __('filament-package_ffhs_approvals::' . $key, $replace);
    }

}
