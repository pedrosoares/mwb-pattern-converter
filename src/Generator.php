<?php
/**
 * Created by PhpStorm.
 * User: Pedrosoares
 * Date: 2/26/18
 * Time: 1:28 PM
 */

namespace Pedrosoares\MwbPatternConverter;


use TakaakiMizuno\MWBParser\Elements\Table;

class Generator {

    private $table;

    private $schema;

    private $namespace;

    public function __construct(Table $table, string $schema='public', string $namespace='App') {
        $this->table = $table;
        $this->schema = $schema;
        $this->namespace = $namespace;
    }

    public function generateController(){
        /**
         * @var string
         * Override the fallowing variables
         * @{domainname}
         * @{modelname}
         * @{repositoryname}
         * @{repositoryvarname}
         */
        $controller = Generator::replace([
            "domainname",
            "modelname",
            "repositoryname",
            "repositoryvarname",
            "namespace"
        ], [
            Generator::Capitalize($this->schema),
            Generator::Capitalize($this->table->getName()),
            Generator::Capitalize($this->table->getName())."Repository",
            strtolower(Generator::Capitalize($this->table->getName()))."Repository",
            $this->namespace
        ], Templates::$controller);

        return $controller;
    }

    public function generateModel(){
        $modelForeingKeys = "";

        foreach ($this->table->getForeignKey() as $foreignKey) {

            if(count($foreignKey->getColumns()) > 1 || count($foreignKey->getReferenceColumns()) > 1){
                die("Fuck You!");
            }

            $localId = $foreignKey->getColumns()[0]->getName();
            $ReferenceId = $foreignKey->getReferenceColumns()[0]->getName();

            //Verifica se é 1 para muitos ou muitos para 1
            if($localId == $this->table->getColumns()[0]->getName()){
                $modelForeingKeys .= Generator::replace([
                    "targettable",
                    "targetid"
                ], [
                    Generator::Capitalize($foreignKey->getReferenceTableName()),
                    $ReferenceId
                ], Templates::$modelHasMany);
            }else{
                $modelForeingKeys .= Generator::replace([
                    "targettable",
                    "localid"
                ], [
                    Generator::Capitalize($foreignKey->getReferenceTableName()),
                    $localId
                ], Templates::$modelBelongsTo);
            }
        }



        /**
         * @var string
         * Override the fallowing variables
         * @{domainname}
         * @{modelname}
         * @{schemaname}
         * @{modelforeings}
         */
        $model = Generator::replace([
            "domainname",
            "modelname",
            "tablename",
            "schemaname",
            "modelforeings",
            "namespace"
        ],[
            Generator::Capitalize($this->schema),
            Generator::Capitalize($this->table->getName()),
            $this->table->getName(),
            $this->schema,
            $modelForeingKeys,
            $this->namespace
        ], Templates::$model);

        return $model;
    }

    public function generateRepository(){
        /**
         * @var string
         * Override the fallowing variables
         * @{domainname}
         * @{modelname}
         */
        $repository = Generator::replace([
            "domainname",
            "modelname",
            "namespace"
        ], [
            Generator::Capitalize($this->schema),
            Generator::Capitalize($this->table->getName()),
            $this->namespace
        ], Templates::$repository);

        return $repository;
    }

    public static function replace($varname, $value, $string){
        $strName = array_map(function($value) {
            return '{'.$value.'}';
        }, $varname);
        return str_replace($strName, $value, $string);
    }

    public static function Capitalize($classname){
        return str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $classname))));
    }

}