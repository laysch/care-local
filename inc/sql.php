<?php 
    $host = 'sql107.infinityfree.com';    
    $data = 'if0_38360278_carelocal'; 
    $user = 'if0_38360278';         
    $pass = 'P0giRtiAC6eHo';        
    $chrs = 'utf8mb4';
    $attr = "mysql:host=$host;dbname=$data;charset=$chrs";
    $opts =
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
?>