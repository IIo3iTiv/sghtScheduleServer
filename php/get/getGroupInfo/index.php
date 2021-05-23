<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $host = 'localhost';
    $user = 'APopovcev';
    $password = '3NYD-a78C9h78@Y';
    $db_name = 'sght_schedule';

    $link = mysqli_connect($host, $user, $password, $db_name)
        or die("Error connection");
        
    $query = 'SELECT Id, shortName, coursStudy
              FROM sgroup ';

    $result = mysqli_query($link, $query) 
        or die(mysqli_error($link));

    $data = array();
    while ($row = mysqli_fetch_assoc($result))
    $data[] = $row;

    header('Content-Type: application/json');
    $json = json_encode($data);
    echo $json;
}

?>