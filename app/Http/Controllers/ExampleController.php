<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Http\Responses\JsendResponse;
use App\Http\Actions\ExampleAction;
use App\Data\ExampleData;
use App\Services\ApiResponseMaker;

class ExampleController extends Controller
{
    public function public(ApiRequest $request, ExampleAction $action, ExampleData $t0)
    {
        /*
            $t1 = ExampleData::from($request->all());

            $t2 = ExampleData::from(request()->all());

            $request->setDataClass(ExampleData::class);

            $t3 = $request->getData();

            // ---
            
            $r0 = $this->runActionAndCreateResponse($action, request()->all());

            $r1 = $this->runActionAndCreateResponse($action, $t0->toArray());

            $r2 = $this->runActionAndCreateResponse($action, $t1->toArray());

            $r3 = $this->runActionAndCreateResponse($action, $t2->toArray());

            $r4 = $this->runActionAndCreateResponse($action, $t3->toArray());

            $r5 = $this->runActionAndCreateResponse($action, ['str1' => 'str1', 'str2' => 'str2']); // forced

            // ---

            $actionResult = $this->runAction($action, $tX->toArray());

            $r6 = ApiResponseMaker::makeApiResponse($actionResult);

            // ---

            TRY: <hostname>/api/example/public?str1=aaa&str2=bbb
        */

        $actionResult = $this->runAction($action, $t0->toArray());
        //$actionResult = $this->runAction($action, []);

        return ApiResponseMaker::makeApiResponse($actionResult);
    }

    public function public2(ApiRequest $request, ExampleAction $action)
    {
        // Fields are specified only inside the DTO class required by action - TRY: <hostname>/api/example/public2?str1=aaa&str2=bbb

        return $this->runActionAndCreateResponse($action, $request->all());
        //return $this->runActionAndCreateResponse($action, []);
    }

    public function bearerProtected(Request $request)
    {
        return JsendResponse::success([
            'method' => __METHOD__
        ]);
    }

    public function clientBearerProtected(Request $request)
    {
        return JsendResponse::success([
            'method' => __METHOD__
        ]);
    }

    public function userBearerProtected(Request $request)
    {
        return JsendResponse::success([
            'method' => __METHOD__
        ]);
    }
}
