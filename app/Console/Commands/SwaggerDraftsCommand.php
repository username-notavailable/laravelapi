<?php

namespace App\Console\Commands;

use Fuzzy\Fzpkg\Console\Commands\BaseCommand;
use cebe\openapi\Reader;
use cebe\openapi\spec\{Schema, Operation};
use InvalidArgumentException;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Literal;

final class SwaggerDraftsCommand extends BaseCommand
{
    protected $signature = 'swagger:drafts { file : Swagger file [JSON/YAML] }';

    protected $description = 'Create DRAFTS for Controllers/Actions and DTOs from a swagger.json file';

    public function handle(): void
    {
        $swaggerFile = base_path('/' . $this->argument('file'));

        if (!file_exists($swaggerFile)) {
            $this->fail('Swagger file "' . $swaggerFile . '" not exists');
        }

        $fileExt = strtolower(pathinfo($swaggerFile)['extension']);

        switch ($fileExt) {
            case 'json':
                $openApi = Reader::readFromJsonFile($swaggerFile);
                break;

            case 'yaml':
            case 'yml':
                $openApi = Reader::readFromYamlFile($swaggerFile);
                break;

            default:
                throw new InvalidArgumentException('Unsupported "' . $fileExt . '" file (valid extensions .json .yaml .yml)');
        }

        $openApi->validate();
        $errors = array_merge([], $openApi->getErrors());

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->outLabelledError($error);
            }

            exit(1);
        }

        $controllers = ['NoTagsController' => []];

        foreach($openApi->paths as $pathUri => $pathData) {
            foreach ($pathData->getOperations() as $operation) {
                $baseName = implode('', explode('_', ucwords(preg_replace(['@_###_.*$@'], [''], $operation->operationId), '_')));
                
                if (count($operation->tags) === 0) {
                    $selectedController = 'NoTagsController';
                }
                else {
                    if ($operation->tags[0] === 'Controller') {
                        // Controller is invalid as controller name
                        $selectedController = 'BaseController';
                    }
                    else {
                        $matches = [];

                        preg_match('@.+Controller$@', $operation->tags[0], $matches);

                        if (empty($matches)) {
                            $selectedController = ucfirst($operation->tags[0] . 'Controller');
                        }
                        else {
                            $selectedController = ucfirst($operation->tags[0]);
                        }
                    }
                }

                if (!array_key_exists($selectedController, $controllers)) {
                    $controllers[$selectedController] = [];
                }

                $controllers[$selectedController][] = ['baseName' => $baseName, 'operation' => $operation];
            }
        }

        // --- Generate DTO

        foreach ($controllers as $controllerName => $methodsData) {
            if (empty($methodsData)) {
                continue;
            }

            foreach ($methodsData as $methodData) {
                $this->createDtoFromOperation($methodData['baseName'], $methodData['operation']);
            }
        }

        // --- Generate Actions

        foreach ($controllers as $controllerName => $methodsData) {
            if (empty($methodsData)) {
                continue;
            }

            foreach ($methodsData as $methodData) {
                $actionName = $methodData['baseName'];

                $outputFilePath = app_path('Http/Actions/' . $actionName . 'Action.php');

                if (file_exists($outputFilePath)) {
                    $this->outLabelledWarning('Action "' . $actionName . 'Action" already exists');
                    continue;
                }
                else {
                    if (empty($methodData['parameters'])) {
                        $this->callSilent('make:api:action', ['name' => $actionName]);
                    }
                    else {
                        $dtoName = $methodData['baseName'];
                        $this->callSilent('make:api:action', ['name' => $actionName, '--dto' => $dtoName]);
                    }

                    $this->outLabelledSuccess('Action "' . $actionName . '" created');
                }
            }
        }

        // --- Generate Controllers

        foreach ($controllers as $controllerName => $methodsData) {
            if (empty($methodsData)) {
                continue;
            }

            $outputFilePath = app_path('Http/Controllers/' . $controllerName . '.php');

            if (file_exists($outputFilePath) && false) {
                $this->outLabelledWarning('Controller "' . $controllerName . '" already exists');
                continue;
            }
            else {
                $file = new \Nette\PhpGenerator\PhpFile;
                $file->setStrictTypes();

                $namespace = $file->addNamespace('App\Http\Controllers');
                $namespace->addUse('Illuminate\Contracts\Support\Responsable');

                $class = $namespace->addClass(ucfirst($controllerName))->setExtends('App\Http\Controllers\Controller')->addComment("Controller generated by swagger:import command");

                foreach ($methodsData as $methodData) {
                    $actionNamespace = 'App\Http\Actions\\' . $methodData['baseName'] . 'Action';

                    $namespace->addUse($actionNamespace);

                    $method = $class->addMethod(lcfirst($methodData['baseName']))
                        ->setReturnType('Illuminate\Contracts\Support\Responsable')
                        ->setBody('return $this->runActionAndCreateResponse($action, request()->all());');

                    $method->addParameter('action')->setType($actionNamespace);
                }

                file_put_contents($outputFilePath, (new \Nette\PhpGenerator\PsrPrinter)->printFile($file));

                $this->outLabelledSuccess('Controller "' . $controllerName . '" created');
            }
        }
    }

    private function createDtoFromSchema(string $dtoName, Schema $schema) : void
    {
        $outputFilePath = app_path('DTOs/' . $dtoName . '.php');

        if (file_exists($outputFilePath) && false) {
            $this->outLabelledWarning('DTO "' . $dtoName . '" already exists');
        }
        else {
            $file = new PhpFile;
            $file->setStrictTypes();

            $namespace = $file->addNamespace('App\DTOs');
            $namespace->addUse('Spatie\LaravelData\Dto');

            $class = $namespace->addClass(ucfirst($dtoName))->setExtends('Spatie\LaravelData\Dto')->addComment("DTO generated by swagger:import command");
            $construct = $class->addMethod('__construct');

            $required = $schema->required ?? [];

            foreach ($schema->properties as $propertyName => $propertyData) {
                $type = $propertyData->type;

                if ($type !== 'object') {
                    [$phpType, $default] = $this->toPhpType($type);
                }
                else {
                    $phpType = 'App\DTOs\\' . $dtoName . ucfirst($propertyName);
                    $this->createDtoFromSchema($dtoName . ucfirst($propertyName), $propertyData);
                }

                if (!in_array($propertyName, $required)) {
                    $phpType = '?' . $phpType;
                }

                $comment = '';

                if (!empty($description)) {
                    $comment = $description;
                }

                $publicParameter = $construct->addPromotedParameter($propertyName);

                if (isset($default)) {
                    $publicParameter->setType($phpType, $default)->setComment(trim($comment));
                }
                else {
                    $publicParameter->setType($phpType)->setComment(trim($comment));
                }
            }

            file_put_contents($outputFilePath, (new \Nette\PhpGenerator\PsrPrinter)->printFile($file));

            $this->outLabelledSuccess('DTO "' . $dtoName . '" created');
        }
    }

    private function createDtoFromOperation(string $dtoName, Operation $operation) : void
    {
        $outputFilePath = app_path('DTOs/' . $dtoName . '.php');

        if (file_exists($outputFilePath) && false) {
            $this->outLabelledWarning('DTO "' . $dtoName . '" already exists');
        }
        else {
            $file = new PhpFile;
            $file->setStrictTypes();

            $namespace = $file->addNamespace('App\DTOs');
            $namespace->addUse('Spatie\LaravelData\Dto');

            $class = $namespace->addClass(ucfirst($dtoName))->setExtends('Spatie\LaravelData\Dto')->addComment("DTO generated by swagger:import command");
            $construct = $class->addMethod('__construct');

            foreach ($operation->parameters as $parameter) {
                if ((!empty($parameter->schema) && !empty($parameter->content)) || (empty($parameter->schema) && empty($parameter->content))) {
                    throw new InvalidArgumentException('Parameter Objects MUST include either a content field or a schema field, but not both (dto = "' . $dtoName . '")');
                }

                $name = $parameter->name;
                $in = $parameter->in;
                $required = $parameter->required ?? false;
                $description = $parameter->description ?? '';
                $deprecated = $parameter->deprecated ?? false;

                if (!empty($parameter->schema)) {
                    $type = $parameter->schema->type;
                    $schema = $parameter->schema;
                }
                else if (!empty($parameter->content)) {
                    $type = $parameter->content[array_keys($parameter->content)[0]]->schema->type;
                    $schema = $parameter->content[array_keys($parameter->content)[0]]->schema;
                }

                if ($type !== 'object') {
                    [$phpType, $default] = $this->toPhpType($type);
                }
                else {
                    $phpType = 'App\DTOs\\' . $dtoName . ucfirst($name);
                    $this->createDtoFromSchema($dtoName . ucfirst($name), $schema);
                }

                if ($required !== true) {
                    $phpType = '?' . $phpType;
                }

                $comment = '';

                if (!empty($deprecated)) {
                    $comment .= 'DEPRECATED ';
                }

                if (!empty($description)) {
                    $comment .= $description;
                }

                $publicParameter = $construct->addPromotedParameter($name);

                if ($in === 'path') {
                    $namespace->addUse('Spatie\LaravelData\Attributes\FromRouteParameter');
                    $publicParameter->addAttribute('Spatie\LaravelData\Attributes\FromRouteParameter', [$name]);
                }

                if (isset($default)) {
                    $publicParameter->setType($phpType, $default)->setComment(trim($comment));
                }
                else {
                    $publicParameter->setType($phpType)->setComment(trim($comment));
                }
            }

            if (!empty($operation->requestBody)) {
                if (empty($operation->requestBody->content)) {
                    throw new InvalidArgumentException('RequestBody Objects MUST include content field (dto = "' . $dtoName . '")');
                }

                $name = 'requestBody';
                $mediaType = array_keys($operation->requestBody->content)[0];
                $required = $operation->requestBody->required ?? false;
                $description = $operation->requestBody->description ?? '';
                
                $phpType = 'App\DTOs\\' . $dtoName . ucfirst($name);
                $this->createDtoFromSchema($dtoName . ucfirst($name), $operation->requestBody->content[$mediaType]->schema);

                if ($required !== true) {
                    $phpType = '?' . $phpType;
                }

                $comment = '';

                if (!empty($description)) {
                    $comment = $description;
                }

                $publicParameter = $construct->addPromotedParameter($name);

                if (isset($default)) {
                    $publicParameter->setType($phpType, $default)->setComment(trim($comment));
                }
                else {
                    $publicParameter->setType($phpType)->setComment(trim($comment));
                }
            }

            file_put_contents($outputFilePath, (new \Nette\PhpGenerator\PsrPrinter)->printFile($file));

            $this->outLabelledSuccess('DTO "' . $dtoName . '" created');
        }
    }

    protected function toPhpType(string $type) : array
    {
        return match ($type) {
            'string' => ['string', ''],
            'number' => ['float', 0],
            'integer' => ['int', 0],
            'boolean' => ['bool', true],
            'array' => ['array', []],
            'object' => ['StdClass', Literal::new('StdClass')]
        };
    }
}
