<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Validators;

class AutotraderCompetitorStockValidator extends AbstractAutotraderValidator
{
    /**
     * {@inheritDoc}
     *
     * Validates competitor stock search filter parameters.
     * See: https://developers.autotrader.co.uk/api#search-api
     *
     * The searchType=competitor parameter enforces a maximum page size of 20
     * and allows pagination up to 10 pages (200 vehicles total).
     */
    protected function getRules(): array
    {
        return [
            'page' => 'nullable|integer|min:1|max:10',
            'pageSize' => 'nullable|integer|min:1|max:20',

            'valuations' => 'nullable|boolean',

            'standardMake' => 'nullable|string',
            'standardModel' => 'nullable|string',
            'standardTrim' => 'nullable|string',
            'standardTransmissionType' => 'nullable|string',
            'standardFuelType' => 'nullable|string',
            'standardBodyType' => 'nullable|string',
            'standardDrivetrain' => 'nullable|string',

            'minPlate' => 'nullable|integer|min:0',
            'maxPlate' => 'nullable|integer|min:0',

            'minEnginePowerBHP' => 'nullable|numeric|min:0',
            'maxEnginePowerBHP' => 'nullable|numeric|min:0',

            'minBadgeEngineSizeLitres' => 'nullable|numeric|min:0',
            'maxBadgeEngineSizeLitres' => 'nullable|numeric|min:0',

            'doors' => 'nullable|integer|min:0',

            'registration' => 'nullable|string',

            'postcode' => 'nullable|string',
            'radius' => 'nullable|integer|min:0',
        ];
    }

    protected function getMessages(): array
    {
        return [
            'pageSize.max' => 'Competitor stock searches are limited to a maximum page size of 20.',
            'page.max' => 'Competitor stock searches are limited to a maximum of 10 pages.',
        ];
    }
}
