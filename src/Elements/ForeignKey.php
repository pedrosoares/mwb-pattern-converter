<?php
namespace Pedrosoares\MwbPatternConverter\Elements;

use SebastianBergmann\CodeCoverage\Report\PHP;

class ForeignKey extends \TakaakiMizuno\MWBParser\Elements\ForeignKey {

    /**
     * @param \Pedrosoares\MwbPatternConverter\Elements\Table[] $tables
     */
    public function resolveReferencedTableAndColumnBackwards($tables){
        $this->referencedTableName = $tables["source"]->getName();
        foreach ($tables["target_id"] as $column){
            $this->referencedColumns[] = $column;
        }
        foreach ($tables["id"] as $column){
            $this->columns[] = $column;
        }
    }

}
