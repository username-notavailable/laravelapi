<?php

namespace App\Http\Controllers;

use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Client;
use Fuzzy\Fzpkg\Classes\Clients\KeyCloak\Classes\KeyCloakSsoClientTrait;


abstract class Controller
{
    use KeyCloakSsoClientTrait;
    
    protected $kcClient;

    public function __construct() 
    {
        $this->kcClient = Client::create();
    }
}
