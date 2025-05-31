<?php

use App\DTOs\ActionResult;
use App\DTOs\Enums\ActionResultStatus;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;
use App\Services\ApiResponseMaker;

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
            if (config('app.debug')) {
                $data['exception'] = HttpException::class;
                $data['exceptionMessage'] = $e->getMessage();
                $data['requestHeaders'] = $request->header();
                $data['trace'] = explode('<## FZ_SEP ##>', str_replace("\n", '<## FZ_SEP ##>', $e->getTraceAsString()));
            }

            return ApiResponseMaker::makeApiResponse(ActionResult::from([
                'status' => ActionResultStatus::ERROR, 
                'payload' => $data, 
                'httpCode' => $e->getStatusCode(), 
                'errorCode' => $e->getStatusCode(), 
                'humanErrorMessage' => __('Errore nell\'elaborazione della richiesta')
            ]));
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            $data['errors'] = $e->errors();

            if (config('app.debug')) {
                $data['exception'] = ValidationException::class;
                $data['exceptionMessage'] = $e->getMessage();
                $data['requestHeaders'] = $request->header();
                $data['trace'] = explode('<## FZ_SEP ##>', str_replace("\n", '<## FZ_SEP ##>', $e->getTraceAsString()));
            }

            return ApiResponseMaker::makeApiResponse(ActionResult::from([
                'status' => ActionResultStatus::FAIL, 
                'payload' => $data, 
                'httpCode' => $e->status
            ]));
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            $data = [
                'exception' => get_class($e)
            ];

            if (config('app.debug')) {
                $data['exceptionMessage'] = $e->getMessage();
                $data['requestHeaders'] = $request->header();
                $data['trace'] = explode('<## FZ_SEP ##>', str_replace("\n", '<## FZ_SEP ##>', $e->getTraceAsString()));
            }

            return ApiResponseMaker::makeApiResponse(ActionResult::from([
                'status' => ActionResultStatus::ERROR, 
                'payload' => $data, 
                'httpCode' => 500, 
                'humanErrorMessage' => __('Errore nell\'elaborazione della richiesta')
            ]));
        });
    })->create();