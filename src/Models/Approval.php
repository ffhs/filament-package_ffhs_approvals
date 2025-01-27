<?php

namespace Ffhs\Approvals\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends BaseApproval
{
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('filament-package_ffhs_approvals.tables.approvals', 'approvals');
    }

    public static function userHasApproved(Model $record, string $statusClass, string $category): bool
    {
        return self::where('category', $category)
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->where('approver_id', auth()->id())
            ->whereIn('status', $statusClass::getApprovedStatuses())
            ->exists();
    }
}
