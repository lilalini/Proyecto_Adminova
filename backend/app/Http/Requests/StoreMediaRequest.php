<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
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
            'model_type' => 'required|string|in:App\Models\Accommodation,App\Models\Owner,App\Models\Guest,App\Models\User',
            'model_id' => 'required|integer',
            'collection_name' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'file_name' => 'required|string|max:255',
            'file_path' => 'required|string',
            'file_size' => 'required|integer|min:0',
            'mime_type' => 'required|string|max:100',
            'disk' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_main' => 'boolean',
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];
    }
}
