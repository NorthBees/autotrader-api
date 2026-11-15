<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

/**
 * Retail advert channel statuses (`adverts.retailAdverts.{advertiserAdvert,autotraderAdvert,
 * profileAdvert,exportAdvert}.status`).
 *
 * REJECTED (7 Dec 2026): Truck, Motorhome and Caravan stock whose make or model fails
 * Autotrader's standardisation is listed as REJECTED instead of PUBLISHED until the make
 * and/or model is updated to an accepted value.
 */
enum AutotraderAdvertStates: string
{
    case PUBLISHED = 'PUBLISHED';
    case NOT_PUBLISHED = 'NOT_PUBLISHED';
    case REJECTED = 'REJECTED';
}
