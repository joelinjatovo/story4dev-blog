<?php

function s4d_log($index, $message){
    return;
    
    if( !is_dir(S4D_DIR."log") && !mkdir(S4D_DIR."log") ){
        return;
    }
    
    if(is_array($message)){
        $message = json_encode($message, JSON_PRETTY_PRINT);
    }
    
    if(is_object($message)){
        $message = json_encode($message, JSON_PRETTY_PRINT);
    }
    
    ob_start();
    echo date('Y-m-d h:i:s') . ' ' . $index . ' ' . $message . PHP_EOL;
    $log = ob_get_contents();
    ob_end_clean();
    
    $file = S4D_DIR."log/debug".date('Ymd').".log";
    $current = $log . "\n";
    @file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
}
