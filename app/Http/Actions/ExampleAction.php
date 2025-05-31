<?php

namespace App\Http\Actions;

use App\DTOs\ActionResult;
use App\DTOs\Enums\ActionResultStatus;
use Spatie\LaravelData\Dto;
use App\DTOs\ExampleDto;

class ExampleAction extends ApiActionAbstract
{
    public function handle(?Dto $dto = null) : ActionResult
    {
        return ActionResult::from(['status' => ActionResultStatus::SUCCESS, 'httpCode' => 200, 'payload' => ['str1' => $dto->str1, 'str2' => $dto->str2]]);
    }

    public function getRequestedDtoClass() : ?string
    {
        return ExampleDto::class;
    }
}