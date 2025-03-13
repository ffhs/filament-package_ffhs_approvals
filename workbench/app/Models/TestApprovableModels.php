<?php

namespace App\Models;

use App\Approvals\TestApprovalStatuses;
use Ffhs\Approvals\Approval\SimpleApprovalBy;
use Ffhs\Approvals\Approval\SimpleApprovalFlow;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Traits\HasApprovals;
use Illuminate\Database\Eloquent\Model;

class TestApprovableModels extends Model implements Approvable
{
    use HasApprovals;

    public function getApprovalFlows(): array
    {
        return [
            'test-key-1' => SimpleApprovalFlow::make()
                ->approvalStatus(TestApprovalStatuses::cases())
                ->approvalBy([
                    SimpleApprovalBy::make('admin')->atLeast(1),
                ]),

        ];
    }


}
