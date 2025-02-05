<?php

namespace Ffhs\Approvals\Models;

use Attribute;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
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


    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (string $value){
                return $value; //ToDo
            },
            set: function (string|UnitEnum $value){
                if($value instanceof UnitEnum) return $value->value;
                else return $value;
            }
        );
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
