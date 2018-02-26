<?php
namespace Pedrosoares\MwbPatternConverter\Elements;

use Pedrosoares\MwbPatternConverter\Generator;

class Table extends \TakaakiMizuno\MWBParser\Elements\Table {


    public function resolveBackwardForeignKeyReference($reference, $tables){

        $fakeXmlColumns = "<link type=\"object\">{id}</link>\n";

        /**
         * @var string
         * Override the fallowing variables
         * @{tableid}
         * @{columns}
         * @{tableownerid}
         * @{ownercolumns}
         */
        $fakeXml = <<<EOF
<value type="object" struct-name="db.mysql.ForeignKey" id="2C5C91D3-AB27-47AE-89EB-E9DA8747507E" struct-checksum="0x70a8fc40">
      <link type="object" struct-name="db.mysql.Table" key="referencedTable">{tableid}</link>
      <value _ptr_="0x6080006badc0" type="list" content-type="object" content-struct-name="db.Column" key="columns">
        {columns}      </value>
      <value _ptr_="0x6080008a4bc0" type="dict" key="customData"/>
      <value type="int" key="deferability">0</value>
      <value type="string" key="deleteRule">NO ACTION</value>
      <link type="object" struct-name="db.Index" key="index">2AE0D0B6-FAAB-4215-AD69-3B24413E30E6</link>
      <value type="int" key="mandatory">1</value>
      <value type="int" key="many">1</value>
      <value type="int" key="modelOnly">0</value>
      <link type="object" struct-name="db.Table" key="owner">{tableownerid}</link>
      <value _ptr_="0x6080006bd6a0" type="list" content-type="object" content-struct-name="db.Column" key="referencedColumns">
        {ownercolumns}      </value>
      <value type="int" key="referencedMandatory">1</value>
      <value type="string" key="updateRule">NO ACTION</value>
      <value type="string" key="comment"></value>
      <value type="string" key="name">{foreingkeyname}</value>
      <value type="string" key="oldName">{foreingkeyname}</value>
    </value>
EOF;

        $columns = "";

        foreach ($reference["id"] as $id){
            $columns .= Generator::replace([
                "id"
            ],[
                $id->getId()
            ], $fakeXmlColumns);
        }

        $sourceColumns = "";

        foreach ($reference["target_id"] as $id){
            $sourceColumns .= Generator::replace([
                "id"
            ],[
                $id->getId()
            ], $fakeXmlColumns);
        }

        $xml = Generator::replace([
            "tableid",
            "columns",
            "tableownerid",
            "ownercolumns",
            "foreingkeyname"
        ],[
            "",
            "",
            "",
            "",
            "a".rand(0, 9999)."a"
        ], $fakeXml);

        $this->foreignKeys[] = new ForeignKey(new \SimpleXMLElement($xml), true);

        $this->foreignKeys[count($this->foreignKeys)-1]->resolveReferencedTableAndColumnBackwards($reference);

    }
}
