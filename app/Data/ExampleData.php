<?php

namespace App\Data;

use Spatie\LaravelData\Data;

class ExampleData extends Data
{
    public function __construct(
        public ?string $str1 = null,
        public ?string $str2 = null
    ) {}

    /*public static function rules() : array
    {
        return [
            'str1' => 'required|string',
            'str2' => 'required|string'
        ];
    }*/
}