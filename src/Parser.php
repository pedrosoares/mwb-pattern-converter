<?php
namespace Pedrosoares\MwbPatternConverter;

use Pedrosoares\MwbPatternConverter\Elements\Table;

class Parser extends \TakaakiMizuno\MWBParser\Parser {

    /** @var string */
    private $dbname;

    protected function parseXML() {

        $tables = $this->data->xpath('//value[@struct-name="db.mysql.Table"]');
        foreach ($tables as $table) {
            $this->tables[] = new Table($table);
        }
        $tableIds = [];
        foreach ($this->tables as $table) {
            $tableIds[$table->getId()] = $table;
        }
        foreach ($this->tables as $table) {
            $table->resolveForeignKeyReference($tableIds);
        }
        $this->parseForeignKeyBackward($tableIds);

        $this->dbname = (string) $this->data->xpath('/data[@grt_format="2.0"]/value[@type="object"]/value[3]/value[@type="object"]/value[1]/value[2]/value[@type="object"]//value[16]/text()')[0];
    }

    /**
     * This function create a ForeignKey on a table that is referenced by another ForeignKey
     * Example:
     * If Book has a ForeignKey to User so create a User ForeignKey to books
     */
    private function parseForeignKeyBackward($tables){
        $createOn = [];
        foreach ($this->tables as $table) {
            if(count($table->getForeignKey()) > 0){
                foreach ($table->getForeignKey() as $ForeignKey){
                    $createOn[] = [
                        "name" => $ForeignKey->getReferenceTableName(),
                        "source" => $table,
                        "id" => $ForeignKey->getReferenceColumns(),
                        "target_id" => $ForeignKey->getColumns(),
                        "object" => $ForeignKey
                    ];
                }
            }
        }

        foreach ($this->tables as $table) {
            foreach ($createOn as $localtable){
                if($localtable["name"] == $table->getName()){
                    $table->resolveBackwardForeignKeyReference($localtable, $tables);
                }
            }
        }
    }

    public function getDbName(){
        return $this->dbname;
    }
}
