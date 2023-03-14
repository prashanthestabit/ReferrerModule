<?php

namespace Modules\ReferrerModule\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\ReferrerModule\Http\Requests\ReferrerStoreRequest;
use Modules\ReferrerModule\Repositories\ReferrerModuleRepository;

class ReferrerModuleController extends Controller
{

    protected $referrer;

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
                'message' => __('paymentgatewaymanagement::messages.try_again'),
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
        try
        {
            $userId = $this->referrer->getUserData(['referrer_id'=> $request->input('referral_code')]);

            $data = [
                'referrer_name' => $request->input('referrer_name'),
                'referrer_email' => $request->input('referrer_email'),
                'referred_name' => $request->input('referred_name'),
                'referred_email' => $request->input('referred_email'),
                'referral_code'  => $request->input('referral_code'),
                'user_id'        => $userId,
                'created_at'     => now()
            ];

            $rs = $this->referrer->saveReferral($data);

            if ($rs) {
                $responseData = [
                    'status' => true,
                    'message' => __('referrermodule::messages.try_again'),
                    'data' => $rs,
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            } else {
                $responseData = [
                    'status' => false,
                    'message' => __('referrermodule::messages.try_again'),
                ];
                return $this->referrer->responseMessage($responseData, Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());

            $responseData = [
                'status' => false,
                'message' => __('paymentgatewaymanagement::messages.try_again'),
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
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('referrermodule::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
