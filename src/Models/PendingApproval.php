<?php

namespace Ffhs\Approvals\Models;

class PendingApproval extends BaseApproval
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('filament-package_ffhs_approvals.tables.pendings', 'pendings');
    }
}
