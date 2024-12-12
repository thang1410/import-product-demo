<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls'
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Không được để trống',
            'file.file' => 'Không hợp lệ',
            'file.mimes' => 'Định dạng file không hợp lệ',
        ];
    }
}
