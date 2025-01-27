<?php

namespace Ffhs\Approvals\Models;

use Ffhs\Approvals\Infolists\Actions\ApprovalAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $category
 * @property string $approvable_type
 * @property int $approvable_id
 * @property string $status
 * @property Carbon|null $status_at
 * @property int|null $approver_id
 * @property array|null $config
 */
class BaseApproval extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'approvable_type',
        'approvable_id',
        'status',
        'status_at',
        'approver_id',
        'config',
    ];

    protected $casts = [
        'status_at' => 'datetime',
        'status' => 'string',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(config('filament-package_ffhs_approvals.models.approver'), 'approver_id');
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo('approvable');
    }

    public static function getApprovalForAction(ApprovalAction $action): ?BaseApproval
    {
        return self::where('category', $action->getCategory())
            ->where('approvable_type', get_class($action->getRecord()))
            ->where('approvable_id', $action->getRecord()->getKey())
            ->where('approver_id', auth()->id())
            ->first();
    }

    public static function userHasResponded(Model $record, string $category): bool
    {
        return self::where('category', $category)
            ->where('approvable_type', get_class($record))
            ->where('approvable_id', $record->getKey())
            ->where('approver_id', auth()->id())
            ->whereNot('status', null)
            ->exists();
    }
}
