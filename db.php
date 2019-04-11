<?php
    $env = file_exists(__DIR__ . '/..' . '/environment.json') ? file_get_contents(__DIR__ . '/..' . '/environment.json') : null;

    $dbHost = 'localhost'; 
    $dbUsername = 'root';
    $dbName = 'w9market_report'; 
    $dbPassword = ''; 
    $port = '3306'; 
    $connection = 'pdo'; 

    if($env) {
        try {
            $envVars = json_decode($env);
            $dbHost = $envVars->host;
            $dbUsername = $envVars->username;
            $dbPassword = $envVars->password;
            $dbName = $envVars->name;
            $port = $envVars->port;
            $connection = $envVars->connection;
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }

    if($connection == 'pdo'){
      $conn = new PDO(
            'mysql:dbname=' . $dbName . ';host=' . $dbHost . ';port=' . $port . 'options=\'-c client_encoding=utf8\'"',
            $dbUsername,
            $dbPassword
        );
     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     
    }

    else if($connection == 'pg'){
        $conn_string = "host=" . $dbHost . " port=" . $port . " dbname=" . $dbName . " user=" . $dbUsername . " 
                   password=" . $dbPassword;
        $conn = pg_connect($conn_string);
    } else {
        $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $port);
    }

    //Table names//
    $bi_marketing = 'bi_marketing';
    $postcode_list = 'postcode_list';
    $national_trend = 'national_trend';
?>
