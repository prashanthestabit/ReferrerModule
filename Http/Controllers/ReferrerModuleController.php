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
use Modules\ReferrerModule\Repositories\ReferrerModuleRepository;

class ReferrerModuleController extends Controller
{

    protected $referrer;

    const TRYAGAIN = 'Sorry!! Try again';

    public function __construct(ReferrerModuleRepository $referrer)
    {
        $this->referrer = $referrer;
    }

    /**
     * get Referrer Id from user table
     */
    public function getReferrerId()
    {
        try
        {
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
                'message' => self::TRYAGAIN,
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);

        }
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ReferrerStoreRequest $request)
    {
        try {
            $data =  $this->getData($request);

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
                    'message' => self::TRYAGAIN,
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => self::TRYAGAIN,
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('referrermodule::show');
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ReferrerUpdateRequest $request, $id)
    {
        try {

            $data =  $this->getData($request);

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
                    'message' => self::TRYAGAIN,
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => self::TRYAGAIN,
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }
    }

    protected function getData($request)
    {
        $user = $this->referrer->getUserData(['referrer_id'=> $request->input('referred_code')]);

        return [
            'referrer_name' => $request->input('referrer_name'),
            'referrer_email' => $request->input('referrer_email'),
            'referred_name' => $request->input('referred_name'),
            'referred_email' => $request->input('referred_email'),
            'referral_code'  => $request->input('referred_code'),
            'user_id'        => ($user)?$user->id:null,
            'created_at'     => now()
        ];
    }

    /**
     * Remove the specified resource from storage.
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
                    'message' => self::TRYAGAIN,
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }

        } catch (Exception $e)
        {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => self::TRYAGAIN,
            ];
            return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
        }

    }
}
