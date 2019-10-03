<?php
//change to FALSE if you want back to appgini default
$globals = '{
    "app-title-prefix"  :"Ale | ", 
    "logo-mini"         :"glyphicon glyphicon-tags", 
    "logo-mini-text"    :"LTE", 
    "navbar-text"       :"Alejandro Landini template AdminLTE",
    "footer-left-text"  :"<strong>ALE © '. date("Y") .' <a href=\"#\">Alejandro Landini <small>admin template from LTE Admin</small></a>.</strong>",
    "footer-right-text" :"Anything you want"
}';

$LTE_globals = json_decode($globals,true);

 //changue this for tablename icon
 $ico_menu = '{
    "logins":"fa fa-table",
    "Locations":"fa fa-gift",
    "Pencil":"fa fa-pencil-square-o",
    "Cog":"fa fa-cog",
    "Plus":"fa fa-plus",
    "slash":"fa fa-eye-slash"
}';

function getLteStatus($LTE_enable = true){
    if(!function_exists('getMemberInfo')){
        $LTE_enable = false;
    } 
    return $LTE_enable ;
}
