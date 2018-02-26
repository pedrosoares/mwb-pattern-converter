<?php
/**
 * Created by PhpStorm.
 * User: Pedrosoares
 * Date: 2/26/18
 * Time: 1:05 PM
 */

namespace Pedrosoares\MwbPatternConverter;


class Templates {

    /**
     * @var string
     * Override the fallowing variables
     * @{domainname}
     * @{modelname}
     */
    public static $repository = <<<TAG
<?php
/**
 * User: Pedrosoares
 * Date: 13/09/17
 * Time: 15:23
 */

namespace {namespace}\\Domains\\{domainname}\\Repositories;


use Illuminate\\Validation\\ValidationException;
use {namespace}\\Domains\\{domainname}\\Entities\\{modelname};
use {namespace}\\Domains\\Security\\Entities\\Condition;
use {namespace}\\Support\\Repository\\Operation\\Create;
use {namespace}\\Support\\Repository\\Operation\\Read;
use {namespace}\\Support\\Repository\\Operation\\Update;
use {namespace}\\Support\\Repository\\Operation\\Delete;
use {namespace}\\Support\\Repository\\Repository;

class {modelname}Repository extends Repository {

    use Create, Read, Update, Delete;

    protected \$modelClass = {modelname}::class;

}
    
TAG;

    /**
     * @var string
     * Override the fallowing variables
     * @{domainname}
     * @{modelname}
     * @{tablename}
     * @{schemaname}
     * @{modelforeings}
     */
    public static $model = <<<EOT
<?php

namespace {namespace}\Domains\{domainname}\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use {namespace}\Core\Exceptions\DependencyException;
use {namespace}\Domains\Root\Auditable;
use {namespace}\Domains\Root\Contracts\Auditable as AuditableContract;

class {modelname} extends Model implements AuditableContract {
    
    use Auditable;
    
    protected \$table = "{schemaname}.{tablename}";
    
    {modelforeings}
    
}

EOT;

    /**
     * @var string
     * Override the fallowing variables
     * @{targettable}
     * @{targetid}
     */
    public static $modelHasMany = <<<EOT

    public function {targettable}(){
        return \$this->hasMany({targettable}::class, '{targetid}');
    }

EOT;

    /**
     * @var string
     * Override the fallowing variables
     * @{targettable}
     * @{localid}
     */
    public static $modelBelongsTo = <<<EOT

    public function {targettable}(){
        return \$this->belongsTo({targettable}::class, '{localid}');
    }

EOT;


    /**
     * @var string
     * Override the fallowing variables
     * @{domainname}
     * @{modelname}
     * @{repositoryname}
     * @{repositoryvarname}
     */
    public static $controller = <<<EOT
<?php
/**
 * Created by PhpStorm.
 * User: Pedrosoares
 * Date: 10/10/17
 * Time: 15:15
 */

namespace {namespace}\Domains\{domainname}\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use {namespace}\Core\Http\Controllers\Controller;
use {namespace}\Domains\{domainname}\Entities\{modelname};
use {namespace}\Domains\{domainname}\Repositories\{repositoryname};

class {modelname}Controller extends Controller {

    /**
     * @var {modelname}Repository
     */
    protected \${repositoryvarname};

    public function __construct({repositoryname} \${repositoryvarname}) {
        \$this->{repositoryvarname} = \${repositoryvarname};
    }

    /**
     * TODO Validate the actions with Policy
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Pagination\AbstractPaginator
     */
    public function index(){
        return \$this->{repositoryvarname}->getAll();
    }

    /**
     * TODO Validate the actions with Policy
     * @param int \$id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function show(int \$id){
        return \$this->{repositoryvarname}->findByID(\$id);
    }

    /**
     * @param Request \$request
     * @return array|JsonResponse
     */
    public function store(Request \$request){
        /** TODO add validation */
        \$this->validate(\$request, [
            
        ]);

        try{
            \$this->{repositoryvarname}->beginTransaction();
            /**
             * @var {modelname} \$model
             */
            \$model = \$this->{repositoryvarname}->create(\$request->all());


            \$this->{repositoryvarname}->commit();
        }catch (\Exception \$exception){
            \$this->{repositoryvarname}->rollback();

            Log::error(\$exception);
            /**
             * TODO Handle this exception later
             */
            return new JsonResponse([
                "error" => "Erro interno, tente novamente ou mais tarde. (Código do Erro: " . \$exception->getCode() . ")"
            ], 500);
        }

        return [
            "success" => "Cadastrado com Sucesso! Verifique seu e-mail para continuar."
        ];
    }

    /**
     * TODO Validate the actions with Policy
     * @param int \$id
     * @param Request \$request
     * @return array|JsonResponse
     */
    public function update(int \$id, Request \$request){
        /** TODO add validation */
        \$this->validate(\$request, [

        ]);


        try{
            \$this->{repositoryvarname}->beginTransaction();

            \$model = \$this->{repositoryvarname}->findByID(\$id);

            \$this->{repositoryvarname}->update(\$model, \$request->all());



            \$this->{repositoryvarname}->commit();
        }catch (\Exception \$exception) {
            \$this->{repositoryvarname}->rollback();

            if(\$exception->getCode() == 422){
                \$error = \$exception->getMessage();
            }else{
                \$error = "Erro interno, tente novamente ou mais tarde. (Código do Erro: " . \$exception->getCode() . ")";
            }

            Log::error(\$exception);
            /**
             * TODO Handle this exception later
             */
            return new JsonResponse([
                "error" => \$error
            ], 500);
        }
        return [
            "success" => "Usuário atualizado com sucesso!"
        ];
    }

    /**
     * TODO Validate the actions with Policy
     * @param int \$id
     * @return array|JsonResponse
     */
    public function destroy(int \$id){
        try{
            \$this->{repositoryvarname}->beginTransaction();

            \$model = \$this->{repositoryvarname}->findByID(\$id);
            \$this->{repositoryvarname}->delete(\$model);

            \$this->{repositoryvarname}->commit();
        }catch (\Exception \$exception) {
            \$this->{repositoryvarname}->rollback();

            /**
             * TODO Handle this exception later
             */
            return new JsonResponse([
                "error" => "Erro interno, tente novamente ou mais tarde. (Código do Erro: " . \$exception->getCode() . ")"
            ], 500);
        }
        return [
            "success" => "Usuário deletado com sucesso!"
        ];
    }

}

EOT;


}