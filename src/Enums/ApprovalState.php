<?php

namespace Ffhs\Approvals\Enums;

enum ApprovalState
{
    case APPROVED;
    case DECLINED;
    case PENDING;
    case OPEN;
}
