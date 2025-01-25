<?php

namespace Ffhs\Approvals\Models;

use Ffhs\Approvals\Infolists\Actions\ApprovalAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InvalidArgumentException;

/**
 * @property string $status
 */
class Approval extends Model
{
    protected $fillable = [
        'category',
        'scope',
        'approvable_type',
        'approvable_id',
        'status',
        'approver_id',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'status' => 'string',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('filament-package_ffhs_approvals.tables.approvals');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(config('filament-package_ffhs_approvals.models.approver'), 'approver_id');
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo('approvable');
    }

    public function scopeApprovable(Builder $query, string $approvableType, int $approvableId): Builder
    {
        return $query->where('approvable_type', $approvableType)->where('approvable_id', $approvableId);
    }

    public function allInCategoryApproved(string $enumClass, string $category, ?string $scope = null): bool
    {
        // Validate that the provided class exists and is an enum
        if (! class_exists($enumClass) || ! method_exists($enumClass, 'cases')) {
            throw new InvalidArgumentException('The provided class must be a valid enum.');
        }

        $approvedStatuses = $enumClass::getApprovedStatuses();

        if (empty($approvedStatuses)) {
            return false;
        }

        $query = $this->where('category', $category)
            ->when($scope, fn ($query) => $query->where('scope', $scope));

        $totalRecords = $query->count();
        $approvedRecords = $query->whereIn('status', $approvedStatuses)->count();

        return $totalRecords > 0 && $totalRecords === $approvedRecords;
    }

    public function anyInCategoryDeclined(string $enumClass, string $category, ?string $scope = null): bool
    {
        // Validate that the provided class exists and is an enum
        if (! class_exists($enumClass) || ! method_exists($enumClass, 'cases')) {
            throw new InvalidArgumentException('The provided class must be a valid enum.');
        }

        $declinedStatuses = $enumClass::getDeclinedStatuses();

        if (empty($declinedStatuses)) {
            return false;
        }

        return $this->where('category', $category)
            ->when($scope, fn ($query) => $query->where('scope', $scope))
            ->whereIn('status', $declinedStatuses)
            ->exists();
    }

    public static function getApprovalForAction(ApprovalAction $action): ?Approval
    {
        return self::where('category', $action->getCategory())
            ->where('scope', $action->getScope())
            ->where('approvable_type', get_class($action->getRecord()))
            ->where('approvable_id', $action->getRecord()->getKey())
            ->where('approver_id', auth()->id())
            ->first();
    }

    public static function userHasApproved(Model $record, string $statusClass, string $category, ?string $scope = null): bool
    {
        return self::where('category', $category)
            ->when($scope, fn ($query) => $query->where('scope', $scope))
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->where('approver_id', auth()->id())
            ->whereIn('status', $statusClass::getApprovedStatuses())
            ->exists();
    }

    public static function userHasResponded(Model $record, string $category, ?string $scope = null): bool
    {
        return self::where('category', $category)
            ->when($scope, fn ($query) => $query->where('scope', $scope))
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->where('approver_id', auth()->id())
            ->whereNot('status', '')
            ->exists();
    }
}
