<?php

namespace Utils;

function getConst($path){
    $globalArray = [
        'db' => [
            'address' => 'sql201.epizy.com',
            'user' => 'epiz_34266507',
            'pass' => 'jrORzKKMTf',
            'dbname' => 'epiz_34266507_scheduler'
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