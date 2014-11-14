<?php
require_once '../vendor/autoload.php';

function debug_print($args){
    if (empty($args)) {
        return;
    }
    $cont = 0;
    $type= 'plain';
    if ($args[$cont]=='html'){
        $cont++;
        $type = 'html';
    }
    @header("Content-Type: text/$type; charset=utf-8");
    $func = $args[$cont]=='vd'?'var_dump':'print_r';
    if($type=='html') echo'<pre style="background-color: #fff">';
    foreach ($args as $arg) {
        $func($arg);
        echo "\n";flush();
    }
    echo "\n<br>##### ".(memory_get_usage(true)/(1024*1024))."##### \n<br>";
    echo "\n<br>##### ".(memory_get_usage()/(1024*1024))."##### \n<br>";
    if($type=='html') echo'</pre>';
}
function h()
{
    $args = func_get_args();
    array_unshift($args, 'html');
    debug_print($args);
}
function p()
{
    debug_print(func_get_args());
}
function pe(){
    debug_print(func_get_args());
    exit();
}