<?php

declare(strict_types=1);

use NorthBees\AutotraderApi\Support\VehicleResponseNormaliser;

it('passes the legacy vehicle root through untouched', function (): void {
    $legacy = getAutotraderFixture('vehicles-legacy.json');

    expect(VehicleResponseNormaliser::normalise($legacy))->toBe($legacy);
});

it('passes non-array input through untouched', function (mixed $input): void {
    expect(VehicleResponseNormaliser::normalise($input))->toBe($input);
})->with([null, 'ok', 42, true]);

it('lifts every record block to the root', function (): void {
    $flat = VehicleResponseNormaliser::normalise(getAutotraderFixture('vehicles-results.json'));
    $legacy = getAutotraderFixture('vehicles-legacy.json');

    foreach (['vehicle', 'features', 'motTests', 'chargeTimes', 'valuations', 'check', 'history', 'advertiser', 'metadata'] as $block) {
        expect($flat)->toHaveKey($block, $legacy[$block]);
    }

    expect($flat)->not->toHaveKey('results');
});

it('reports totalResults', function (): void {
    expect(VehicleResponseNormaliser::normalise(getAutotraderFixture('vehicles-results.json')))
        ->toHaveKey('totalResults', 1);
});

it('leaves no vehicle key when there are no results', function (): void {
    $flat = VehicleResponseNormaliser::normalise(getAutotraderFixture('vehicles-results-empty.json'));

    // `empty($response['vehicle'] ?? [])` is how callers detect an unknown registration.
    expect($flat)
        ->not->toHaveKey('vehicle')
        ->toHaveKey('totalResults', 0);
});

it('derives totalResults from the results count when the key is absent', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'results' => [['vehicle' => ['registration' => 'DC64AGZ']]],
    ]);

    expect($flat)->toHaveKey('totalResults', 1);
});

it('takes the first record when the API returns more than one', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'results' => [
            ['vehicle' => ['registration' => 'FIRST']],
            ['vehicle' => ['registration' => 'SECOND']],
        ],
        'totalResults' => 2,
    ]);

    expect($flat)
        ->toHaveKey('vehicle', ['registration' => 'FIRST'])
        ->toHaveKey('totalResults', 2);
});

it('tolerates a results value that is not a list', function (): void {
    expect(VehicleResponseNormaliser::normalise(['results' => 'unexpected']))
        ->toHaveKey('totalResults', 0)
        ->not->toHaveKey('vehicle');

    expect(VehicleResponseNormaliser::normalise(['results' => ['a' => ['vehicle' => ['registration' => 'DC64AGZ']]]]))
        ->toHaveKey('vehicle', ['registration' => 'DC64AGZ']);
});

it('merges duplicated warnings exactly once during the overlap window', function (): void {
    $flat = VehicleResponseNormaliser::normalise(getAutotraderFixture('vehicles-results.json'));

    expect($flat['warnings'])->toHaveCount(2)
        ->and($flat['serviceWarnings'])->toBe([
            ['message' => 'Token: [Partner Details] is not allowed to access endpoint: [vehicles]'],
        ])
        ->and($flat['recordWarnings'])->toBe([
            ['feature' => 'valuations', 'message' => 'Cannot source derivative information for DC64AGZ'],
        ]);
});

it('keeps the service and record split once warnings stop being duplicated', function (): void {
    $flat = VehicleResponseNormaliser::normalise(getAutotraderFixture('vehicles-results-post-oct.json'));

    expect($flat['warnings'])->toHaveCount(2)
        ->and($flat['serviceWarnings'])->toBe([
            ['message' => 'Token: [Partner Details] is not allowed to access endpoint: [vehicles]'],
        ])
        ->and($flat['recordWarnings'])->toBe([
            ['feature' => 'valuations', 'message' => 'Cannot source derivative information for DC64AGZ'],
        ]);
});

it('de-duplicates warnings regardless of key order', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'results' => [[
            'vehicle' => ['registration' => 'DC64AGZ'],
            'warnings' => [['feature' => 'valuations', 'message' => 'No valuation']],
        ]],
        'warnings' => [['message' => 'No valuation', 'feature' => 'valuations']],
    ]);

    expect($flat['warnings'])->toHaveCount(1)
        ->and($flat['serviceWarnings'])->toBe([]);
});

it('handles warnings entries that are plain strings', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'results' => [['vehicle' => [], 'warnings' => ['record warning']]],
        'warnings' => ['service warning', 'record warning'],
    ]);

    expect($flat['serviceWarnings'])->toBe(['service warning'])
        ->and($flat['recordWarnings'])->toBe(['record warning'])
        ->and($flat['warnings'])->toBe(['service warning', 'record warning']);
});

it('omits the warnings key entirely when there are none', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'results' => [['vehicle' => ['registration' => 'DC64AGZ']]],
        'totalResults' => 1,
    ]);

    expect($flat)
        ->not->toHaveKey('warnings')
        ->toHaveKey('serviceWarnings', [])
        ->toHaveKey('recordWarnings', []);
});

it('lets the record win when a key exists at both levels', function (): void {
    $flat = VehicleResponseNormaliser::normalise([
        'vehicle' => ['registration' => 'ROOT'],
        'results' => [['vehicle' => ['registration' => 'RECORD']]],
    ]);

    expect($flat)->toHaveKey('vehicle', ['registration' => 'RECORD']);
});
