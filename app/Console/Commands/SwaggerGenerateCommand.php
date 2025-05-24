<?php

namespace App\Console\Commands;

use Fuzzy\Fzpkg\Console\Commands\BaseCommand;
use Fuzzy\Fzpkg\Classes\SweetApi\Classes\SwaggerEndpoints;


final class SwaggerGenerateCommand extends BaseCommand
{
    protected $signature = 'swagger:generate { --base_uri= : Server URL }';

    protected $description = 'Generate swagger.json file';

    public function handle(): void
    {
        SwaggerEndpoints::generateSwaggerJson((!empty($this->option('base_uri')) ? parse_url($this->option('base_uri')) : []), public_path('swagger/swagger.json'));
    }
}
