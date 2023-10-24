<?php

namespace App\Domain\Subscription\Requests;

use App\Domain\Shared\Rules\ValidCityRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city_name' => ['required', 'string', new ValidCityRule()],
            'precipitation_threshold_mm' => ['required', 'integer'],
            'uv_threshold' => ['required', 'integer'],
        ];
    }
}
