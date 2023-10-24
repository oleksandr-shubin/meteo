<?php

namespace App\Domain\Shared\Rules;

use App\Domain\Weatherapi\Support\WeatherapiService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCityRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $weatherapiService = app()->make(WeatherapiService::class);

        if ($weatherapiService->isValidCity($value)) {
            return;
        }

        $fail('The :attribute is not supported');
    }
}
