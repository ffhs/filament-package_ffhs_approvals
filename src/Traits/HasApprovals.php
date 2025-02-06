<?php

namespace Ffhs\Approvals\Traits;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasApprovals
{

    public function __get($key)
    {
        if(str_contains($key, 'is_approved_')) {
            $key = str_replace('is_approved_', '', $key);
            $flow =   $this->getApprovalFlows()[$key] ?? null;
            if(is_null($flow)) return parent::__get($key);
            return $flow->approved($this, $key) == ApprovalState::APPROVED;
        }
        if(str_contains($key, 'is_denied_')) {
            $key = str_replace('is_denied_', '', $key);
            $flow =   $this->getApprovalFlows()[$key] ?? null;
            if(is_null($flow)) return parent::__get($key);
            return $flow->approved($this, $key) == ApprovalState::DENIED;
        }
        if(str_contains($key, 'is_pending_')) {
            $key = str_replace('is_pending_', '', $key);
            $flow =   $this->getApprovalFlows()[$key] ?? null;
            if(is_null($flow)) return parent::__get($key);
            return $flow->approved($this, $key) == ApprovalState::DENIED;
        }
        return parent::__get($key);
    }


    public function approvals(): MorphMany
    {
        return $this->morphMany(config('filament-package_ffhs_approvals.models.approvals', Approval::class), 'approvable');
    }


    public function approvalStatistics(?array $categories = null, ?array $keys = null): array
    {

        $flows = $this->getFiltertApprovalFlow($categories, $keys);
        $statistic = [];

        foreach ($flows as $key => $flow) {

            $flowStatistic = [];
            $byStatistics = [];

            $statusStatistics = [];
            $approvedStatistics = [];

            $approvedStatisticsMap =[];
            foreach ($flow->getApprovalStatus()[0]::getApprovedStatuses() as $status) {
                $approvedStatisticsMap[$status->value] = 'approved';
            }
            foreach ($flow->getApprovalStatus()[0]::getDeniedStatuses() as $status) {
                $approvedStatisticsMap[$status->value] = 'declined';
            }
            foreach ($flow->getApprovalStatus()[0]::getPendingStatuses() as $status) {
                $approvedStatisticsMap[$status->value] = 'pending';
            }


            foreach ($flow->getApprovalBys() as $approvalBy) {
                $states = $approvalBy->getApprovals($this,$key)->pluck('status');

                $states->each(function (string $value) use (&$statusStatistics) {
                    $statusStatistics[$value] = ($statusStatistics[$value]??0) + 1;
                });
                $states->each(function (string $value) use ($approvedStatisticsMap, &$approvedStatistics) {
                    $key = $approvedStatisticsMap[$value];
                    $approvedStatistics[$key] = ($approvedStatistics[$key]??0) + 1;
                });



                $byStatistics[] = [
                    'reachedAtLeast' => $approvalBy->reachAtLeast($this, $key),
                    'statues' => $states->toArray(),
                ];
            }

            $flowStatistic['category'] = $flow->getCategory();
            $flowStatistic['byStatistics'] = $byStatistics;
            $flowStatistic['statusStatistics'] = $statusStatistics;
            $flowStatistic['approvedStatistics'] = $approvedStatistics;
            $statistic[$key] = $flowStatistic;
        }


        return $statistic;
    }

    public function getFilteredApprovalFlow(?array $categories = null, ?array $keys = null): array{
        $flows=$this->getApprovalFlows();
        if($keys) $flows = Arr::only($flows, $keys);

        if($categories){
            $flows = Arr::where($flows, function (ApprovalFlow $value, $key) use ($categories) {
                return in_array($value->getCategory(), $categories);
            });
        }
        return $flows;
    }


    public function isDenied(?array $categories = null, ?array $keys = null): bool{
        return $this->approved($categories, $keys) == ApprovalState::DENIED;
    }

    public function isPending(?array $categories = null, ?array $keys = null): bool{
        return $this->approved($categories, $keys) == ApprovalState::PENDING;
    }




    public function approved(?array $categories = null, ?array $keys = null): ApprovalState
    {
        $flows = $this->getFilteredApprovalFlow($categories, $keys);

        $isPending = false;
        $isOpen= false;
        foreach ($flows as $key => $flow) {
            $approved = $flow->approved($this, $key);
            if($approved == ApprovalState::PENDING) $isPending = true;
            elseif($approved == ApprovalState::OPEN) $isOpen = true;
            elseif($approved == ApprovalState::DENIED) return ApprovalState::DENIED;
        }

        if($isPending) return ApprovalState::PENDING;
        elseif($isOpen) return ApprovalState::OPEN;
        return ApprovalState::APPROVED;

    }


    public function isApproved(?array $categories = null, ?array $keys = null): bool{
        return $this->approved($categories, $keys) == ApprovalState::APPROVED;
    }

}
