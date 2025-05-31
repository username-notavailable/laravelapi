<?php

namespace App\Http\Actions;

use Spatie\LaravelData\Dto;
use App\DTOs\ActionResult;

abstract class ApiActionAbstract
{
    /**
     * Do business logic and return a Dto object
     */
    abstract public function handle(?Dto $dto = null) : ActionResult;

    /**
     * Return the requested DTO classname or null
     */
    abstract public function getRequestedDtoClass() : ?string;

    /**
     * Run the action and return an ActionResult
     */
    public function run(array $initDtoData = []) : ActionResult
    {
        $dtoClass = $this->getRequestedDtoClass();

        if (!is_null($dtoClass)) {
            if (!class_exists($dtoClass)) {
                throw new \InvalidArgumentException(__METHOD__ . ': Dto "' . $dtoClass . '" not exists');
            }

            $dto = $dtoClass::validateAndCreate($initDtoData);
        }
        else {
            $dto = null;
        }

        return $this->handle($dto);
    }
}
