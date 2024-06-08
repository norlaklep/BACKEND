<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PlaceCreateRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:100', 'unique:places,title'],
            'description' => ['required', 'string', 'max:400'],
            'address' => ['required', 'string', 'max:255'],
            'address_link' => ['required', 'url', 'max:255'],
            'image_placeholder' => ['required', 'url', 'max:255'],
            'image_gallery' => ['required', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required.',
            'title.string' => 'Title must be a string.',
            'title.max' => 'Title cannot exceed 100 characters.',
            'title.unique' => 'Title has already been taken.',
            'description.required' => 'Description is required.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot exceed 400 characters.',
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a string.',
            'address.max' => 'Address cannot exceed 255 characters.',
            'address_link.required' => 'Address link is required.',
            'address_link.url' => 'Address link must be a valid URL.',
            'address_link.max' => 'Address link cannot exceed 255 characters.',
            'image_placeholder.required' => 'Image placeholder is required.',
            'image_placeholder.url' => 'Image placeholder must be a valid URL.',
            'image_placeholder.max' => 'Image placeholder cannot exceed 255 characters.',
            'image_gallery.required' => 'Image gallery is required.',
            'image_gallery.url' => 'Image gallery must be a valid URL.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors()
        ], 422));
    }
}
