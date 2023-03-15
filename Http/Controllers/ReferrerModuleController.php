<?php

namespace Modules\ReferrerModule\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\ReferrerModule\Http\Requests\ReferrerStoreRequest;
use Modules\ReferrerModule\Http\Requests\ReferrerUpdateRequest;
use Modules\ReferrerModule\Http\Requests\ReferrerUpdateStatusRequest;
use Modules\ReferrerModule\Repositories\ReferrerModuleRepository;

class ReferrerModuleController extends Controller
{

    protected $referrer;

    const TRYAGAIN = 'referrermodule::messages.try_again';

    public function __construct(ReferrerModuleRepository $referrer)
    {
        $this->referrer = $referrer;
    }

    /**
     * retrieves the referral ID associated with the currently authenticated user.
     */
    public function getReferrerId()
    {
        try {
            $referrerId = $this->referrer->getUserField(auth()->user()->id, 'referrer_id');

            $responseData = [
                'status' => true,
                'referrerId' => $referrerId,
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_OK);

        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);

        }
    }

    /**
     * creates a new referral record in the database
     * @param Request $request
     * @return Response
     */
    public function store(ReferrerStoreRequest $request)
    {
        try {
            $data = $this->getData($request);

            $rs = $this->referrer->saveReferral($data);

            if ($rs) {
                $responseData = [
                    'status' => true,
                    'message' => __('referrermodule::messages.referrer.saved'),
                    'data' => $rs,
                ];

                return $this->referrer->responseMessage($responseData, Response::HTTP_OK);
            } else {
                $responseData = [
                    'status' => false,
                    'message' => __(self::TRYAGAIN),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * retrieves information about a user and the number of referrals they have made.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        try {
            $user = $this->referrer->userFindOrFail($id);

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'referrers_count' => $user->referrers_count,
            ];

            $responseData = [
                'status' => true,
                'data' => $data,
            ];

            return $this->referrer->responseMessage($responseData, Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * updates an existing referral record in the database.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(ReferrerUpdateRequest $request, $id)
    {
        try {

            $data = $this->getData($request);

            $rs = $this->referrer->updateReferral($id, $data);

            if ($rs) {
                $responseData = [
                    'status' => true,
                    'message' => __('referrermodule::messages.referrer.updated_success'),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_OK);
            } else {
                $responseData = [
                    'status' => false,
                    'message' => __(self::TRYAGAIN),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getData($request)
    {
        $user = $this->referrer->getUserData(['referrer_id' => $request->input('referred_code')]);

        return [
            'referrer_name' => $request->input('referrer_name'),
            'referrer_email' => $request->input('referrer_email'),
            'referred_name' => $request->input('referred_name'),
            'referred_email' => $request->input('referred_email'),
            'referral_code' => $request->input('referred_code'),
            'user_id' => ($user) ? $user->id : null,
            'created_at' => now(),
        ];
    }

    /**
     * deletes a referral record from the database.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $rs = $this->referrer->deleteReferral($id);

            if ($rs) {
                $responseData = [
                    'status' => true,
                    'message' => __('referrermodule::messages.referrer.delete_success'),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_OK);
            } else {
                $responseData = [
                    'status' => false,
                    'message' => __(self::TRYAGAIN),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * update a referral status from the database
     */
    public function updateStatus(ReferrerUpdateStatusRequest $request)
    {
        try {
            $id = $request->input('id');

            $data = [
                'status' => $request->input('status')
            ];

            $rs = $this->referrer->updateReferral($id, $data);

            if ($rs) {
                $responseData = [
                    'status' => true,
                    'message' => __('referrermodule::messages.referrer.status_updated'),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_OK);
            } else {
                $responseData = [
                    'status' => false,
                    'message' => __(self::TRYAGAIN),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }

        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __(self::TRYAGAIN),
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }
}
