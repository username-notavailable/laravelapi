<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExampleController extends Controller
{
    public function public(Request $request)
    {
        return response(__METHOD__ . ' OK', 200);
    }

    public function bearerProtected(Request $request)
    {
        return response(__METHOD__ . ' OK', 200);
    }

    public function clientBearerProtected(Request $request)
    {
        return response(__METHOD__ . ' OK', 200);
    }

    public function userBearerProtected(Request $request)
    {
        return response(__METHOD__ . ' OK', 200);
    }
}
