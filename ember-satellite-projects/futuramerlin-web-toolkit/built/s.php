<?php

error_reporting(E_ALL);
ini_set("display_errors",1);

if(isset($_REQUEST['q'])) {
    $q = $_REQUEST['q'];
    if(substr($q,0,1) == '!') {
        header("Location: https://duckduckgo.com/html?q=".urlencode($q));
    }
    if($q == 'uuid' || $q == 'guid') {
        header("Location: https://duckduckgo.com/html?q=guid");
    }
    else {
        header("Location: https://www.google.com/search?ncr=1&q=".urlencode($q));
    }
    die();
}
?>