<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\OwnerPayoutMethod;

class StoreOwnerPayoutMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return Gate::allows('create', OwnerPayoutMethod::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'owner_id' => 'required|exists:owners,id',
            'method_type' => 'required|in:bank_transfer,paypal,wise',
            'account_holder' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:50',
            'is_default' => 'boolean',
            'notes' => 'nullable|string',
        ];
    }
}
