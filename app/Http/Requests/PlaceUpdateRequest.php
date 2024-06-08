<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlaceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:400'],
            'image_placeholder' => ['nullable', 'url', 'max:255'],
            'image_gallery' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot exceed 400 characters.',
            'image_placeholder.url' => 'Image placeholder must be a valid URL.',
            'image_placeholder.max' => 'Image placeholder cannot exceed 255 characters.',
            'image_gallery.json' => 'Image gallery must be a valid URL.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
