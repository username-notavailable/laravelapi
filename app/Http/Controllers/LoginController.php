<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Events\LoginRequest;
use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login() 
    {
        try
        {
            if (config('fz.keycloak.login.type') === 'Frontend') {
                $loginUrl = $this->kcClient->getRealmAuthorizationCodeEndpointUrl();
                
                if (is_null($loginUrl)) {
                    Log::debug(__METHOD__ . ': login url non valido "null"');
                    return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Caricamento dati IdP fallito.')]);
                }
                else {
                    Log::debug(__METHOD__ . ': redirect per login a "' . $loginUrl . '"');
                    return redirect($loginUrl);
                }
            }
            else {
                return view(config('fz.keycloak.login.loginViewName'), ['title' => __('Login')]);
            }
        } catch(\Throwable $e) {
            Log::error(__METHOD__ . ': ' . $e->getMessage());
            return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Login fallita.')]);
        }
    }

    public function tryLogin(Request $request)
    {
        if (config('fz.keycloak.login.type') === 'Frontend') {
            return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Modalità di login non supportata.')]);
        }

        $validated = $request->validate([
            'email' => 'required|string|email|max:35',
            'password' => 'required|string|max:35'
        ]);

        $key = Str::transliterate(Str::lower($validated['email'] . '|fz-login|' . $request->ip()));
        $success = false;

        $executed = RateLimiter::attempt(
            $key,
            (int)config('fz.log.login.maxAttempts', 3),
            function() use ($validated, &$success) {
                $success = Auth::guard(config('fz.default.keycloak.authGuardName'))->doUserBackendLogin(['email' => $validated['email'], 'password' => $validated['password']]);
            },
            (int)config('fz.log.login.decaySeconds', 60)
        );
         
        if (!$executed) {
            event(LoginRequest::lockoutResult($request, $validated['email'], 'tryLogin'));

            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }
        else {
            if ($success) {
                RateLimiter::clear($key);
    
                event(LoginRequest::successResult($request, $validated['email'], 'tryLogin'));

                session()->regenerate();

                return redirect()->route(config('fz.keycloak.login.postRouteName'));
            }
            else {
                event(LoginRequest::failResult($request, $validated['email'], 'tryLogin'));

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }
    }

    public function logout(Request $request) 
    {
        try
        {
            if ($request->has('clientIdx') && config('fz.keycloak.logout.allowClientIdxFromRequest')) {
                $validator = Validator::make($request->all(), [
                    'clientIdx' => 'string|max:100'
                ]);
        
                if ($validator->fails()) {
                    Log::error(__METHOD__ . ': Validation failed', $validator->errors());
                    return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Identificativo client non valido.')]);
                }

                $clientIdx = $request->clientIdx;
            }
            else {
                $clientIdx = $this->kcClient->getCurrentKeycloakClientIdx();
            }

            $jsonToken = Auth::guard(config('fz.default.keycloak.authGuardName'))->getClientIdxUserToken($clientIdx);
            
            if (!is_null($jsonToken)) {
                if (config('fz.keycloak.login.type') === 'Frontend') {
                    $idToken = config('fz.keycloak.logout.skipConfirmation') ? (array_key_exists('id_token', $jsonToken) ? $jsonToken['id_token'] : '') : '';
                }
                else {
                    if (array_key_exists('id_token', $jsonToken)) {
                        $idToken = $jsonToken['id_token'];
                    }
                    else {
                        Log::warning(__METHOD__ . ': "id_token" non presente nel token json');
                        return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Dati IdP non validi.')]);
                    }
                }
    
                $logoutUrl = (new Client($clientIdx))->getRealmEndSessioEndpointUrl($idToken, route('fz_end_user_session_callback'));
                    
                if (is_null($logoutUrl)) {
                    Log::debug(__METHOD__ . ': logout url non valido "null"');
                    return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Caricamento dati IdP fallito.')]);
                }
                else {
                    Log::debug(__METHOD__ . ': redirect per logout a "' . $logoutUrl . '"');
                    return redirect($logoutUrl);
                }
            }
            else {
                Log::debug(__METHOD__ . ': lettura del token fallita (clientIdx = "' . $clientIdx . '")');
                return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Lettura del token fallita.')]);
            }
        } catch(\Throwable $e) {
            Log::error(__METHOD__ . ': ' . $e->getMessage());
            return view(config('fz.keycloak.login.issuesViewName'), ['lead' => __('Logout fallita.')]);
        }
    }
}
