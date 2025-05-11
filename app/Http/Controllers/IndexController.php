<?php

namespace App\Http\Controllers;

use Illuminate\Http\{Request, RedirectResponse};
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(Request $request) : RedirectResponse
    {
        if (Auth::guard('keyCloak')->check()) {
            return redirect()->route('dashboard');
        }
        else {
            return redirect()->route('auth_login');
        }
    }

    public function dashboard()
    {
        $userToken = Auth::guard(config('fz.default.keycloak.authGuardName'))->getClientIdxUserToken();
        $userTokenProfile = Auth::guard(config('fz.default.keycloak.authGuardName'))->getClientIdxUserTokenProfile();
        $userTokenProfileId = Auth::guard(config('fz.default.keycloak.authGuardName'))->getClientIdxUserTokenProfileId();
        $decodedUserAccessToken = $this->kcClient->decodeAccessToken($userToken['access_token']);

        $clientToken = $this->kcClient->getClientToken();
        $decodedClientAccessToken = $this->kcClient->decodeAccessToken($clientToken['access_token']);

        return view('dashboard', [
            'title' => __('Dashboard'),
            'userToken' => $userToken,
            'userTokenProfile' => $userTokenProfile,
            'userTokenProfileId' => $userTokenProfileId,
            'decodedUserAccessToken' => $decodedUserAccessToken,
            'clientToken' => $clientToken,
            'decodedClientAccessToken' => $decodedClientAccessToken,
            'sessionData' => session()->all()
        ]);
    }
}
