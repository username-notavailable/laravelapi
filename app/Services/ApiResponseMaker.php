<?php

namespace App\Services;

use App\DTOs\ActionResult;
use Illuminate\Contracts\Support\Responsable;
use App\Http\Responses\JsendResponse;

class ApiResponseMaker
{
    static public function makeApiResponse(ActionResult $actionResult) : Responsable
    {
        //if (config === 'jsend) {
            return JsendResponse::from($actionResult);
        //}
        //else {
            //return ...
        //}
    }
}