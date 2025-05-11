<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Classes\AccessTokenRequestTrait;

class CheckAccessToken
{
    use AccessTokenRequestTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $result = $this->readAccessToken($request);

        if ($result['code'] !== 200) {
            return response($result['reason'], $result['code']);
        }
        else {
            //Replace with custom code
            //
            // $decoded = $result['decoded'];

            return $next($request);
        }
    }
}
