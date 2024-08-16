<?php

$dbcon = true;

function dbcon(){
    $serverName = "localhost\\sqlexpress"; //serverName\instanceName

    $connectionInfo = array("Database" => "DX_WH_OF");
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn == false) {
        echo "Koneksi ke DB gagal.<br />";
        die(print_r(sqlsrv_errors(), true));//untuk mematikan php
    } 

    return $conn;//mengirim hasil agar bisa digunakan diluar function local
}

dbcon();
?>