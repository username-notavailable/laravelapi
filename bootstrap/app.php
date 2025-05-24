<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Responses\ApiResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $e, Request $request) {
            $data = [
                'systemException' => true,
                'exception' => get_class($e)
            ];

            if (config('app.debug')) {
                $data['exceptionMessage'] = $e->getMessage();
                $data['requestHeaders'] = $request->header();
                $data['trace'] = explode('<## FZ_SEP ##>', str_replace("\n", '<## FZ_SEP ##>', $e->getTraceAsString()));
            }

            return ApiResponse::error(
                __('Errore nell\'elaborazione della richiesta'),
                $data,
                $e->getStatusCode()
            )->withHttpCode($e->getStatusCode());
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            $data = [
                'systemException' => true,
                'exception' => get_class($e)
            ];

            if (config('app.debug')) {
                $data['exceptionMessage'] = $e->getMessage();
                $data['requestHeaders'] = $request->header();
                $data['trace'] = explode('<## FZ_SEP ##>', str_replace("\n", '<## FZ_SEP ##>', $e->getTraceAsString()));
            }

            return ApiResponse::error(
                __('Errore nell\'elaborazione della richiesta'),
                $data,
                null
            );
        });
    })->create();