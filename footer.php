<?php
include_once 'LTE/config_lte.php';

    if (getLteStatus()){
        include_once("LTE/footer_lte.php");
    }else{
        include_once("footer_old.php");
    }