<?php

namespace admin {

    class javascriptFunctions {

        public static function buildJQueryTableName($tableCount, $tabNCol) {
            for ($j = 0; $j < $tableCount; $j++) {
                $tempColSet = "";
                //The Logic
                $tableName = $tabNCol[$j][0];
                //***************************************************************************************
                if ($j > 0) {
                    $tempColSet .= ",'$tableName': [";
                } else {
                    $tempColSet .= "'$tableName': [";
                }
                $columnCount = count($tabNCol[$j]);
                //The Logic Looped for Columns
                for ($z = 1; $z < $columnCount; $z++) {
                    if ($z > 1) {
                        $tempColSet .= ",";
                    }
                    $colName = $tabNCol[$j][$z];
                    $tempColSet .= "'$colName'";
                }
                $tempColSet .= "]";
                return $tempColSet;
            }
        }

    }

}

