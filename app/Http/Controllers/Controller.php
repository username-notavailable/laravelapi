<?php

namespace App\Http\Controllers;

use App\DTOs\ActionResult;
use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Client;
use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Classes\KeyCloakSsoClientTrait;
use Illuminate\Contracts\Support\Responsable;
use App\Http\Actions\ApiActionAbstract;
use App\Services\ApiResponseMaker;

abstract class Controller
{
    use KeyCloakSsoClientTrait;
    
    protected $kcClient;

    public function __construct(Client $kcClient) 
    {
        $this->kcClient = $kcClient;
    }

    public function runAction(ApiActionAbstract $action, array $initDtoData = []) : ActionResult
    {
        return $action->run($initDtoData);
    }

    public function runActionAndCreateResponse(ApiActionAbstract $action, array $initDtoData = []) : Responsable
    {
        return ApiResponseMaker::makeApiResponse($this->runAction($action, $initDtoData));
    }
}
