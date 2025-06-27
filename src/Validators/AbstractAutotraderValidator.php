<?php

declare(strict_types=1);

namespace NorthBees\AutotraderApi\Validators;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\ValidationException;

abstract class AbstractAutotraderValidator
{
    protected ValidatorFactory $factory;

    public function __construct()
    {
        $this->factory = app(ValidatorFactory::class);
    }

    /**
     * The rules to validate against
     * see: https://laravel.com/docs/11.x/validation#available-validation-rules.
     *
     * @return array
     */
    abstract protected function getRules(): array;

    /**
     * Perform the validation against the given data
     * returns the validated data.
     *
     * @param array $data
     *
     * @throws BindingResolutionException
     * @throws ValidationException
     *
     * @return array
     */
    public function validate(array $data): array
    {
        $validator = $this->factory->make($data, $this->getRules(), $this->getMessages());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get the validation message overrides for any fields
     * This should be overridden in the child class.
     *
     * @return array
     */
    protected function getMessages(): array
    {
        return [];
    }
}
