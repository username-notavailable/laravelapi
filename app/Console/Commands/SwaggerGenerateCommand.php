<?php

namespace App\Console\Commands;

use Fuzzy\Fzpkg\Console\Commands\BaseCommand;
use Fuzzy\Fzpkg\Classes\SweetApi\Classes\SwaggerEndpoints;


final class SwaggerGenerateCommand extends BaseCommand
{
    protected $signature = 'swagger:generate {base_uri=?}';

    protected $description = 'Generate swagger.json file';

    public function handle(): void
    {
        $baseUri = $this->hasArgument('base_uri') ? $this->argument('base_uri') : config('app.url');

        $outputPath = public_path('swagger/swagger.json');

        SwaggerEndpoints::generateSwaggerJson(parse_url($baseUri), $outputPath);

        //chmod($swaggerJsonFilePath, 0666);
    }
}
