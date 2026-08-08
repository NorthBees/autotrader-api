<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Support;

use Illuminate\Support\Arr;

/**
 * Normalises the Vehicles API response to the historic flat shape.
 *
 * From August 2026 the Vehicles API returns its payload under a `results` array to match
 * the other Autotrader Connect APIs:
 *
 *     {"results": [{"vehicle": {...}, "features": [...]}], "totalResults": 1}
 *
 * The historic `{"vehicle": {...}, "features": [...]}` root is served alongside it until
 * 28 October 2026. This class accepts either and always returns the flat shape, so callers
 * need no changes.
 *
 * Only the Vehicles API is normalised. The Search and Stock APIs return genuinely
 * multi-record `results` arrays and must keep them.
 */
class VehicleResponseNormaliser
{
    /**
     * @param  mixed  $response  The decoded Vehicles API response.
     * @return mixed The flat shape, or the input untouched when it is not the new envelope.
     */
    public static function normalise(mixed $response): mixed
    {
        if (! is_array($response) || ! array_key_exists('results', $response)) {
            return $response;
        }

        $results = is_array($response['results']) ? $response['results'] : [];

        $record = Arr::first($results);
        $record = is_array($record) ? $record : [];

        $rootWarnings = self::warningsOf($response);
        $recordWarnings = self::warningsOf($record);

        // Root-minus-record isolates the true service-level warnings under both regimes:
        // during the overlap the root holds service ∪ record, and after 28 October 2026 it
        // holds service alone. Either way the difference is the service-level set, so
        // concatenating the two below can neither duplicate nor drop a warning.
        $serviceWarnings = array_values(array_udiff(
            $rootWarnings,
            $recordWarnings,
            static fn ($a, $b): int => strcmp(self::canonicalise($a), self::canonicalise($b)),
        ));

        $flat = array_merge(
            Arr::except($response, ['results', 'totalResults', 'warnings']),
            Arr::except($record, ['warnings']),
        );

        $flat['totalResults'] = $response['totalResults'] ?? count($results);
        $flat['serviceWarnings'] = $serviceWarnings;
        $flat['recordWarnings'] = $recordWarnings;

        $warnings = array_merge($serviceWarnings, $recordWarnings);

        if ($warnings !== []) {
            $flat['warnings'] = $warnings;
        }

        return $flat;
    }

    /**
     * @param  array<mixed>  $payload
     * @return array<int, mixed>
     */
    protected static function warningsOf(array $payload): array
    {
        $warnings = $payload['warnings'] ?? null;

        return is_array($warnings) ? array_values($warnings) : [];
    }

    /**
     * Stable representation of a warning, so that key ordering cannot defeat the comparison.
     */
    protected static function canonicalise(mixed $warning): string
    {
        if (is_array($warning)) {
            ksort($warning);
        }

        return (string) json_encode($warning);
    }
}
