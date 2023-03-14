<?php

namespace Modules\ReferrerModule\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReferrerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'referrer_name'  => 'required | string',
            'referrer_email' => 'required | email',
            'referred_name'  => 'required | string',
            'referred_email' => 'required | email',
            'referred_code'  => [
                'required',
                function ($attribute, $value, $fail) {
                    $attrName = $attribute;
                    $count = User::where('referrer_id', $value)->count();
                    if ($count == 0) {
                        $fail(__('referrermodule::messages.referrer.code_not_exist'));
                    }
                },
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => $validator->errors()
        ]));
    }
}
