<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\LaravelData\WithData;

class ApiRequest extends FormRequest
{
    use WithData;

    protected $dataClass = null;

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
        return [];
    }

    public function setDataClass(string $dataClass)
    {
        if (!class_exists($dataClass)) {
            throw new \InvalidArgumentException(__METHOD__ . ': Class "' . $dataClass . '" not exists');
        }

        $this->dataClass = $dataClass;
    }
}
