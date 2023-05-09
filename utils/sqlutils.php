<?php

namespace Utils;

class SQLFormatter {
    public function formatArray($props){
        $resultstring = '';
        
        for($i=0;$i<count($props);$i++){
            $resultstring .= "'";
            $resultstring .= $props[$i];
            $resultstring .= "'";

            if($i+1 < count($props)){
                $resultstring .= ',';
            }
        }

        return $resultstring;
    }

    public function set($property,$value){
        return $property . "='" . $value . "'";
    }
}