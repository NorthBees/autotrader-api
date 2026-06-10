<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

/**
 * Notification setup types available within the notifications object on the
 * Integrations API response (introduced Jun 2026). Each maps to the key used
 * for that notification configuration in the payload.
 */
enum AutotraderNotificationTypes: string
{
    case ADVERTISER = 'advertiserNotification';
    case DEALS = 'dealsNotification';
    case STOCK = 'stockNotification';
}
