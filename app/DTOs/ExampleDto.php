<?php

namespace App\DTOs;

use Spatie\LaravelData\Dto;

class ExampleDto extends Dto
{
    public function __construct(
        public ?string $str1 = null,
        public string $str2 = 'str2'
    ) {}

    /*public static function rules() : array
    {
        return [
            'str1' => 'required|string',
            'str2' => 'required|string'
        ];
    }*/
}