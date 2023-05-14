<?php

namespace NorthBees\AutoTraderApi\Enum;

enum AutoTraderLifecycleStates: string
{
    case DUE_IN = 'DUE_IN';
    case FORECOURT = 'FORECOURT';
    case SALE_IN_PROGRESS = 'SALE_IN_PROGRESS';
    case WASTEBIN = 'WASTEBIN';
    case DELETED = 'DELETED';
    case SOLD = 'SOLD';
}
