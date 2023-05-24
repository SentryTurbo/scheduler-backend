<?php

namespace Utils;

function getConst($path){
    $globalArray = [
        'db' => [
            'address' => 'https://vladsrt.000webhostapp.com/',
            'user' => 'id20798148_root',
            'pass' => 'Rezeknestehnikums123@',
            'dbname' => 'id20798148_scheduler'
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