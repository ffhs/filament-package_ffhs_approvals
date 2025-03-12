<?php

namespace App\Models;

use App\Approvals\TestApprovalStatuses;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Approval\ApprovalFlow;
use Ffhs\Approvals\Contracts\Approvable;
use Ffhs\Approvals\Traits\HasApprovals;
use Illuminate\Database\Eloquent\Model;

class TestApprovableModels extends Model implements Approvable
{
    use HasApprovals;

    public function getApprovalFlows(): array
    {
        return [
            'test-key-1' => ApprovalFlow::make()
                ->approvalStatus(TestApprovalStatuses::cases())
                ->approvalBy([
                    ApprovalBy::make('admin')->atLeast(1),
                ]),

        ];
    }


}
