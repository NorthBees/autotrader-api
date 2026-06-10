<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Enum\AutotraderNotificationTypes;

describe('AutotraderNotificationTypes enum', function () {
    it('has correct values', function () {
        expect(AutotraderNotificationTypes::ADVERTISER->value)->toBe('advertiserNotification');
        expect(AutotraderNotificationTypes::DEALS->value)->toBe('dealsNotification');
        expect(AutotraderNotificationTypes::STOCK->value)->toBe('stockNotification');
    });

    it('has all expected notification types', function () {
        $cases = AutotraderNotificationTypes::cases();
        $values = array_map(fn ($case) => $case->value, $cases);

        expect($values)->toContain('advertiserNotification', 'dealsNotification', 'stockNotification');
        expect($cases)->toHaveCount(3);
    });
})->group('enum', 'notification-types');
