<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Fuzzy\Fzpkg\Traits\SelectThemeTrait;

class ApiNewActionCommand extends GeneratorCommand
{
    use SelectThemeTrait;

    protected $signature = 'make:api:action {name : The name of the action} {--dto= : The dto class}';

    protected $description = 'Create a new api action class';

    protected $type = 'Action';

    private $themeName;

    protected function getStub()
    {
        return base_path('stubs/fz/api-action.stub');
    }

    protected function getPath($name): string
    {
        return app_path('/Http/Actions/' . $this->argument('name') . 'Action.php');
    }

    public function handle(): void
    {
        if ($this->files->missing(base_path('stubs/fz'))) {
            $this->runCommand('fz:install:stubs', [], $this->output);
        }

        parent::handle();
    }

    protected function replaceClass($stub, $name)
    {
        $stub = str_replace('{{ class }}', $this->argument('name'), $stub);

        $dto = $this->option('dto');

        if (empty($dto)) {
            $dto = 'null';
        }
        else {
            if (!class_exists('App\\DTOs\\' . $dto)) {
                $this->fail('Dto class "App\\DTOs\\' . $dto . '" not exists');
            }
            else {
                $dto = "'App\DTOs\$dto'";
            }
        }

        return str_replace('{{ dto }}', $dto, $stub);
    }
}

