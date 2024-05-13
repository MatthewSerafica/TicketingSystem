<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceReportStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => 'required',
            'date_started' => 'required|date',
            'time_started' => 'required|time',
            'ticket_number' => 'required|integer',
            'technician' => 'required|text',
            'requesting_office' => 'required|string',
            'equipment_no' => 'required|string',
            'issue' => 'required|string',
            'action' => 'required|string',
            'recommendation' => 'required|string',
            'date_done' => 'required|date',
            'time_done' => 'required|time',
            'remarks' => 'nullable|string',
        ];
    }
}
