<?php
include_once 'LTE/config_lte.php';

    if (getLteStatus()){
        include_once("LTE/header_lte.php");
    }else{
        include_once("header_old.php");
    }