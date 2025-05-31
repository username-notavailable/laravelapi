<?php

namespace App\DTOs\Enums;

enum ActionResultStatus: string
{
    case SUCCESS = 'success';
    case FAIL = 'fail';
    case ERROR = 'error';
}