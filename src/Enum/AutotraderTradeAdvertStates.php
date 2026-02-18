<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

/**
 * Trade advert publication states for stock items.
 *
 * When updating stock lifecycle state to SOLD and the vehicle has a published tradeAdvert,
 * the tradeAdvert can be marked as NOT_PUBLISHED to unpublish the record.
 */
enum AutotraderTradeAdvertStates: string
{
    case PUBLISHED = 'PUBLISHED';
    case NOT_PUBLISHED = 'NOT_PUBLISHED';
}
