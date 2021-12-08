<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFilesRequest extends FormRequest
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
            'files' => ['required', 'array', 'max:300000'],
            'files' => ['file'],
            'sender' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:65535'],
            'expire_date' => ['required', 'string'],
        ];
    }
}
