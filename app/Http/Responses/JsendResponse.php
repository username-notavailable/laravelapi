<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Spatie\LaravelData\Data;
use App\DTOs\ActionResult;

// https://github.com/omniti-labs/jsend

class JsendResponse extends Data implements Responsable
{
    public function __construct(
        public string $status,
        public int $httpCode = 200,
        public ?array $data = null,
        public ?int $errorCode = null,
        public string $errorMessage = '',
        public array $headers = []
    )
    {}

    // ---

    public static function fromActionResult(ActionResult $actionResult) : JsendResponse
    {
        return new self($actionResult->status->value, $actionResult->httpCode, $actionResult->payload, $actionResult->errorCode, $actionResult->humanErrorMessage, $actionResult->httpHeaders);
    }

    public function withHeaders(array $headers) : self
    {
        $this->headers = $headers;
        return $this;
    }

    public function withHttpCode(int $httpCode) : self
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    public static function success(?array $data = null) : self
    {
        return new self('success', 200, $data);
    }

    public static function fail(?array $data = null) : self
    {
        return new self('fail', 422, $data);
    }

    public static function error(string $errorMessage, ?array $data = null, ?int $errorCode = null) : self
    {
        return new self('error', 500, $data, $errorMessage, $errorCode);
    }

    // ---
    
    public function toResponse($request) : \Illuminate\Http\JsonResponse
    {
        $payload = [];

        $payload['status'] = $this->status;

        if ($this->status === 'error') {
            $payload['message'] = $this->errorMessage;

            if (!is_null($this->errorCode)) {
                $payload['code'] = $this->errorCode;
            }

            if (!is_null($this->data)) {
                $payload['data'] = $this->data;
            }
        }
        else { // success or fail
            $payload['data'] = $this->data;
        }

        return response()->json(
            data: $payload,
            status: $this->httpCode,
            headers: $this->headers,
            options: JSON_UNESCAPED_UNICODE
        );
    }
}