<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HomeProjectRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'content_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'techstack' => 'nullable|array',
            'techstack.*' => 'string',
            'company_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'awards' => 'nullable|string',
            'case_study_link' => 'nullable|string',
            'website' => 'nullable|string',
        ];
    }
}
