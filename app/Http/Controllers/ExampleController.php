<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;

class ExampleController extends Controller
{
    public function public(Request $request)
    {
        return ApiResponse::success([
            'method' => __METHOD__
        ]);
    }

    public function bearerProtected(Request $request)
    {
        return ApiResponse::success([
            'method' => __METHOD__
        ]);
    }

    public function clientBearerProtected(Request $request)
    {
        return ApiResponse::success([
            'method' => __METHOD__
        ]);
    }

    public function userBearerProtected(Request $request)
    {
        return ApiResponse::success([
            'method' => __METHOD__
        ]);
    }
}
