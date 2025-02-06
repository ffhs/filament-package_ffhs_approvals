<?php

namespace Ffhs\Approvals\Enums;

enum ApprovalState
{
    case APPROVED;
    case DENIED;
    case PENDING;
    case OPEN;
}
