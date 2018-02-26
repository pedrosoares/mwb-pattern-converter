<?php
/**
 * Created by PhpStorm.
 * User: Pedrosoares
 * Date: 06/09/17
 * Time: 18:43
 */
namespace Pedrosoares\MwbPatternConverter\Console\Commands;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Pedrosoares\MwbPatternConverter\Generator;
use Pedrosoares\MwbPatternConverter\Parser;

class GenerateCommand extends Command {

    /**
     * Command name
     * @var string
     */
    protected $name = "mwb:generate";

    protected $signature = 'mwb:generate {file : The file path} {--namespace= : The project namespace}';

    public function handle() {
        $this->testParseClass();
    }

    public function testParseClass() {

        $namespace = $this->option('namespace') ?: 'App';

        $filepath = $this->argument("file");

        $file   = $filepath;
        $parser = new Parser($file);
        $tables = $parser->getTables();

        $this->generate($tables, $parser->getDbName(), $namespace);

    }

    function generate($tables, $schema, $namespace) {
        foreach ($tables as $table) {
            $gen = new Generator($table, $schema, $namespace);

            $modelNamespace = Generator::replace([
                "namespace", "domainname"
            ],[
                strtolower($namespace), Generator::Capitalize($schema)
            ], "{namespace}\\Domains\\{domainname}\\Entities\\");
            $controllerNamespace = Generator::replace([
                "namespace", "domainname"
            ],[
                strtolower($namespace), Generator::Capitalize($schema)
            ], "{namespace}\\Domains\\{domainname}\\Http\\Controllers\\");
            $repositoryNamespace = Generator::replace([
                "namespace", "domainname"
            ],[
                strtolower($namespace), Generator::Capitalize($schema)
            ], "{namespace}\\Domains\\{domainname}\\Repositories\\");



            $model = ($gen->generateModel());
            $controller = ($gen->generateController());
            $repository = ($gen->generateRepository());

            $modelNamespace = base_path(str_replace("\\", "/", $modelNamespace));
            $controllerNamespace = base_path(str_replace("\\", "/", $controllerNamespace));
            $repositoryNamespace = base_path(str_replace("\\", "/", $repositoryNamespace));

            File::makeDirectory($modelNamespace, $mode = 0777, true, true);
            File::makeDirectory($controllerNamespace, $mode = 0777, true, true);
            File::makeDirectory($repositoryNamespace, $mode = 0777, true, true);

            file_put_contents($modelNamespace.Generator::Capitalize($table->getName()).".php", $model);
            file_put_contents($controllerNamespace.Generator::Capitalize($table->getName())."Controller".".php", $controller);
            file_put_contents($repositoryNamespace.Generator::Capitalize($table->getName())."Repository".".php", $repository);
        }
    }


}
