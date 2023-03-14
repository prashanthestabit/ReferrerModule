<?php

namespace Modules\ReferrerModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\ReferrerModule\Entities\Referral;

class ReferrerUpdateStatusRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'  => [
                'required',
                function ($attribute, $value, $fail) {
                    $attrName = $attribute;
                    $count = Referral::whereId($value)->count();
                    if ($count == 0) {
                        $fail(__('referrermodule::messages.referrer.id_not_exist'));
                    }
                },
            ],
            'status' => 'required | string',
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
