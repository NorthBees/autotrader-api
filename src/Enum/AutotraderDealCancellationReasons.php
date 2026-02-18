<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Enum;

enum AutotraderDealCancellationReasons: string
{
    case DIFFERENT_VEHICLE = 'Different Vehicle';
    case UNAFFORDABLE = 'Unaffordable';
    case NOT_INTERESTED = 'Not Interested';
    case WENT_ELSEWHERE = 'Went Elsewhere';
    case NOT_AVAILABLE = 'Not Available';
    case CONDITION = 'Condition';
    case POOR_CUSTOMER_SERVICE = 'Poor Customer Service';
    case OTHER = 'Other';

    /**
     * Get all valid cancellation reason values
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
