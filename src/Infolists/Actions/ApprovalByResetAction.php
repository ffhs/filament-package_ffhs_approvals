<?php

namespace Ffhs\Approvals\Infolists\Actions;

use App\Models\User;
use Ffhs\Approvals\Approval\ApprovalBy;
use Ffhs\Approvals\Concerns\HandlesApprovals;
use Ffhs\Approvals\Contracts\ApprovableByComponent;
use Ffhs\Approvals\Contracts\HasApprovalStatuses;
use Ffhs\Approvals\Models\Approval;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ApprovalByResetAction extends Action implements ApprovableByComponent
{
    use HandlesApprovals;





}
