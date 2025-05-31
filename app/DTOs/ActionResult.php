<?php

namespace App\DTOs;

use App\DTOs\Enums\ActionResultStatus;
use Spatie\LaravelData\Dto;

class ActionResult extends Dto
{
    public function __construct(
        public ActionResultStatus $status,
        public int $httpCode = 200,
        public array $httpHeaders = [],
        public array $payloadMetadata = [],
        public ?array $payload = null,
        public ?int $errorCode = null,
        public string $humanErrorMessage = '',
    ) {
    }
}