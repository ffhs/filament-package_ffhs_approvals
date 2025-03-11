<?php

namespace Ffhs\Approvals\Models;

use App\Domain\Approvals\Documents\DocumentApprovalStatus;
use Eloquent;
use Error;
use Exception;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use UnitEnum;

/**
 *
 *
 * @property int $id
 * @property string $key
 * @property string|null $approvable_type
 * @property int|null $approvable_id
 * @property string $status
 * @property string $approval_by
 * @property string $approver_type
 * @property int $approver_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Activity> $activities
 * @property-read int|null $activities_count
 * @property-read Model|Eloquent|null $approvable
 * @property-read Model|Eloquent $approver
 * @method static Builder<static>|Approval newModelQuery()
 * @method static Builder<static>|Approval newQuery()
 * @method static Builder<static>|Approval onlyTrashed()
 * @method static Builder<static>|Approval query()
 * @method static Builder<static>|Approval whereApprovableId($value)
 * @method static Builder<static>|Approval whereApprovableType($value)
 * @method static Builder<static>|Approval whereApprovalBy($value)
 * @method static Builder<static>|Approval whereApproverId($value)
 * @method static Builder<static>|Approval whereApproverType($value)
 * @method static Builder<static>|Approval whereCreatedAt($value)
 * @method static Builder<static>|Approval whereDeletedAt($value)
 * @method static Builder<static>|Approval whereId($value)
 * @method static Builder<static>|Approval whereKey($value)
 * @method static Builder<static>|Approval whereStatus($value)
 * @method static Builder<static>|Approval whereUpdatedAt($value)
 * @method static Builder<static>|Approval withTrashed()
 * @method static Builder<static>|Approval withoutTrashed()
 * @mixin Eloquent
 */
class Approval extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'key',
        'approvable_type',
        'approvable_id',
        'approver_id',
        'approver_type',
        'approval_by',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty();
    }

    public function getTable()
    {
        return config('filament-package_ffhs_approvals.tables.approvals', 'approvals');
    }

    public function approver(): MorphTo
    {
        return $this->morphTo('approver');
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo('approvable');
    }

    public function __get($key)
    {
        return match ($key) {
            'status' => $this->getStatus(),
            default => parent::__get($key),
        };
    }

    public function __set($key, $value)
    {
        match ($key) {
            'status' => $this->setStatus($value),
            default => parent::__set($key, $value),
        };
    }

    protected function getStatus(): HasApprovalStatuses
    {
        $value = parent::__get('status');

        try {
            /** @var Approvable $approvable */
            $approvable = $this->approvable;
            $flow = $approvable->getApprovalFlow($this->key);

            return collect($flow->getApprovalStatus())
                ->firstWhere(fn($unitEnum) => $unitEnum->value == $value);
        } catch (Error|Exception) {
            return $value;
        }
    }

    protected function setStatus(DocumentApprovalStatus|UnitEnum|string $status): void
    {
        if (is_string($status)) {
            parent::__set('status', $status);

            return;
        }

        parent::__set('status', $status->value);
    }

//    public static function userHasApproved(Model $record, string $statusClass, string $category): bool ToDo ???
//    {
//        return self::where('category', $category)
//            ->where('approvable_type', get_class($record))
//            ->where('approvable_id', $record->getKey())
//            ->where('approver_id', auth()->id())
//            ->whereIn('status', $statusClass::getApprovedStatuses())
//            ->exists();
//    }

//    public static function userHasResponded(Model $record, string $category): bool
//    {
//        return self::where('category', $category)
//            ->where('approvable_type', get_class($record))
//            ->where('approvable_id', $record->getKey())
//            ->where('approver_id', auth()->id())
//            ->whereNot('status', null)
//            ->exists();
//    }
}
