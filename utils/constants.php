<?php

namespace Utils;

function getConst($path){
    $globalArray = [
        'db' => [
            'address' => 'localhost',
            'user' => 'root',
            'pass' => '',
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