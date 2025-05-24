<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

// https://github.com/omniti-labs/jsend

class ApiResponse implements Responsable
{
    protected string $status;
    protected int $httpCode;
    protected ?array $data;
    protected ?int $errorCode;
    protected string $errorMessage;
    private array $headers;

    private function __construct(string $status, int $httpCode = 200, ?array $data = null, string $errorMessage = '', ?int $errorCode = null)
    {
        $this->status = $status;
        $this->httpCode = $httpCode;
        $this->data = $data;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->headers = [];
    }

    public function withHeaders(array $headers) : ApiResponse
    {
        $this->headers = $headers;
        return $this;
    }

    public function withHttpCode(int $httpCode) : ApiResponse
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    public static function success(array $data) : ApiResponse
    {
        return new self('success', 200, $data);
    }

    public static function fail(array $data) : ApiResponse
    {
        return new self('fail', 422, $data);
    }

    public static function error(string $errorMessage, ?array $data = null, ?int $errorCode = null) : ApiResponse
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