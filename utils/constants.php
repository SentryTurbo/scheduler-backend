<?php

/*
    Koda apraksts:
        Utilitfails, kurs satur sevi statiskus datus.
        Sobrid tie satur datu bazes autorizacijas datus.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

namespace Utils;

function getConst($path){
    $globalArray = [
        'db' => [
            'address' => 'localhost',
            'user' => 'root',
            'pass' => '', /* '3e1Iosplbci5', */
            'dbname' => 'scheduler'
        ]
    ];

    $req = explode('.', $path);
    
    $accessSection = $globalArray[$req[0]];
    if(count($req) > 1){
        for($i=1;$i<count($req);$i++){
            $accessSection = $accessSection[$req[$i]];
        }
    }
    
    return $accessSection;
}