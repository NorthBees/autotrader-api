<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

enum AutotraderDealNotificationTypes: string
{
    case DEAL_CREATE = 'DEAL_CREATE';
    case DEAL_UPDATE = 'DEAL_UPDATE';
}
