<?php

/*
    Koda apraksts:
        HTTP pieprasijumu headeri. Nepieciesami, lai sistema darbotos.
        Nav loti dross risinajums.
    
    Vlads Muravjovs, 4Ap, Rezeknes Tehnikums, 2023
*/

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Access-Control-Allow-Credentials: true');