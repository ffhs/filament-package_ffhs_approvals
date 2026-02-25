<?php

namespace Ffhs\Approvals\Enums;

enum ApprovalState: string
{
    case APPROVED = 'approved';
    case DENIED = 'denied';
    case PENDING = 'pending';
    case OPEN = 'open';
}
