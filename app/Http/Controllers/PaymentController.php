<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Providers\PortoneService;

class PaymentController extends Controller
{
    private $portoneService;

    public function __construct(PortoneService $portoneService)
    {
        $this->portoneService = $portoneService;
    }

    public function paymentDetail(Request $request)
    {
        $impUid = $request->input('imp_uid');
        if (!$impUid) {
            return response()->json(['error' => 'imp_uid를 입력해 주세요.'], 400);
        }

        $paymentDetails = $this->portoneService->getPaymentDetails($impUid);
        if(isset($paymentDetails['response']['status'])){ //승인상태가 있을 경우
            if($paymentDetails['response']['status'] === "paid"){ //승인일 경우
                $cancelResponse = $this->portoneService->cancelPayment($impUid);

                if(isset($cancelResponse['response']['amount']) && $cancelResponse['response']['amount'] > 0){
                    return response()->json(1); // return code와 message 가 0/null 로 오는 관계로 임시값 넣음
                }
                return response()->json(); // return code와 message 가 0/null 로 오는 관계로 임시값 넣음
            }
            return response()->json($paymentDetails['response']['status']); //승인 이외의 상태
        }else{
            return response()->json($paymentDetails ?? "");
        }

    }
}
