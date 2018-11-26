<?php
    include_once 'home.php';
    
    $home = NEW Home();
    
    echo $home->$_POST['act']();
   
?>