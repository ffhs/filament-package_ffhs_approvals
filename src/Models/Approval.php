<?php

namespace Ffhs\Approvals\Models;

use App\Domain\Approvals\Documents\DocumentApprovalStatus;
use Ffhs\Approvals\Contracts\Approvable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use UnitEnum;

/**
 * @property string key
 * @property string approvable_type
 * @property int approvable_id
 * @property int approver_id
 * @property string approver_type
 * @property string approval_by
 * @property string|UnitEnum status
 * @property Model|Approvable approvable
 */
class Approval extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'key',
        'approvable_type',
        'approvable_id',
        'approver_id',
        'approver_type',
        'approval_by',
        'status',
    ];

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


    protected function getStatus(): DocumentApprovalStatus
    {
        $value = parent::__get('status');
        try {
            $flow = $this->approvable->getApprovalFlows()[$this->key];
            return collect($flow->getApprovalStatus())
                ->firstWhere(fn($unitEnum) =>$unitEnum->value ==  $value);
        }catch (\Error|\Exception){
            return $value;
        }
    }

    protected function setStatus(DocumentApprovalStatus|UnitEnum|string $status): void
    {
      if(is_string($status)){
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


    /**
     * @deprecated
     */
//    public static function getApprovalForAction(ApprovableByComponent $component): ?Approval
//    {
//
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
