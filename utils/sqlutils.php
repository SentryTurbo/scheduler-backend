<?php

/*
    Koda apraksts:
        Satur klasi, kura sevi glaba funkcijas, lai atvieglotu
        SQL vaicajumu izveidi caur kodu.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

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