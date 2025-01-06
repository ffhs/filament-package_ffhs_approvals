<?php

namespace Ffhs\Approvals\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use InvalidArgumentException;

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
        $this->table = config('approvals.tables.approvals');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(config('approvals.models.approver'), 'approver_id');
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
}
