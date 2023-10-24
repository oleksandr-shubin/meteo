<?php

namespace App\Domain\Subscription\Requests;

use App\Domain\Shared\Rules\ValidCityRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
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
            'precipitation_threshold_mm' => ['required', 'integer', 'min: 1'],
            'uv_threshold' => ['required', 'integer', 'min: 1'],
            'pause_for' => ['nullable', 'integer', 'max:24', 'min: 0'],
        ];
    }
}
