<?php

namespace App\Console\Commands;

use Fuzzy\Fzpkg\Console\Commands\BaseCommand;
use Fuzzy\Fzpkg\Classes\SweetApi\Classes\ApiRoutes;

final class RoutesGenerateCommand extends BaseCommand
{
    protected $signature = 'routes:generate';

    protected $description = 'Generate api routes file from controllers';

    public function handle(): void
    {
        $outputPath = base_path('routes/controllers_routes.php');

        ApiRoutes::createRoutesFile(base_path(), $outputPath);

        //chmod($swaggerJsonFilePath, 0666);
    }
}
