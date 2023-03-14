<?php

namespace Modules\ReferrerModule\Repositories;

use App\Models\User;
use Modules\ReferrerModule\Entities\Referral;
use Modules\ReferrerModule\Interface\ReferrerModuleInterface;

/* Class StripeRepository.
 * This class is responsible for handling stripe operations related.
 */
class ReferrerModuleRepository implements ReferrerModuleInterface
{
    /**
     * Generate a response with the given status, message, data and status code.
     *
     * @param array $responseData
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseMessage($responseData, $statusCode)
    {
        return response()->json($responseData, $statusCode);
    }

    public function getUserField($id, $fieldName)
    {
        return User::whereId($id)->pluck($fieldName)->first();
    }


    public function getUserData($condition)
    {
        return User::where($condition)->first();
    }

    public function saveReferral($data)
    {
        return Referral::create($data);
    }

}
