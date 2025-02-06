<?php

namespace Ffhs\Approvals\Traits;

use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Enums\ApprovalState;
use Ffhs\Approvals\Models\Approval;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait HasApprovals
{


    public function approvals(): MorphMany
    {
        return $this->morphMany(config('filament-package_ffhs_approvals.models.approvals', Approval::class), 'approvable');
    }


    public function approvalStatistics(?array $categories = null, ?array $keys = null): array
    {

        $flows = $this->getApprovalFlows();
        if($keys) $flows = Arr::only($flows, $keys);

        if($categories){
            $flows = Arr::where($flows, function (ApprovalFlow $value, $key) use ($categories) {
               return in_array($value->getCategory(), $categories);
            });
        }

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
            foreach ($flow->getApprovalStatus()[0]::getDeclinedStatuses() as $status) {
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


    public function anyDenied(?array $categories = null, ?array $keys = null): bool{
        if($this->approved($categories, $keys) == ApprovalState::DECLINED) return true;
    }

}
