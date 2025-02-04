<?php

namespace App\Models;

use App\Approvals\TestApprovalStatuses;
use Ffhs\Approvals\ApprovalFlow;
use Ffhs\Approvals\Traits\HasApprovals;
use Illuminate\Database\Eloquent\Model;

class TestAprovableModel extends Model
{
    use HasApprovals;

    public function getApprovalFlows(): array
    {
        return [
            'test-approval' => ApprovalFlow::make()
                ->approvalStatus(TestApprovalStatuses::cases())

        ];
    }


}
