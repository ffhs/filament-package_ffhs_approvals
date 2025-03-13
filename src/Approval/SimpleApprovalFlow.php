<?php

namespace Ffhs\Approvals\Approval;

use Ffhs\Approvals\Concerns\HandlesApprovalFlow;
use Ffhs\Approvals\Contracts\ApprovalFlow;
use Ffhs\Approvals\Traits\Approval\CanBeDisabled;
use Ffhs\Approvals\Traits\Approval\HasApprovalBy;
use Ffhs\Approvals\Traits\Approval\HasApprovalStatus;
use Ffhs\Approvals\Traits\Approval\HasCategory;
use Filament\Support\Concerns\EvaluatesClosures;

class SimpleApprovalFlow implements ApprovalFlow
{
    use HasCategory;
    use HasApprovalBy;
    use HasApprovalStatus;
    use EvaluatesClosures;
    use CanBeDisabled;
    use HandlesApprovalFlow;

    public static function make(): static
    {
        $approvalFlow = app(static::class);
        $approvalFlow->setUp();

        return $approvalFlow;
    }

    protected function setUp()
    {
    }


}
