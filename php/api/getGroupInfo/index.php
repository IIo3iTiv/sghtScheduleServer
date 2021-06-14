<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['course'])) $course = htmlentities($_GET['course']);

    $isEmpty = false;
    if (empty($course) || $course < 1 || $course > 4) $isEmpty = true;

    if (!$isEmpty) {

        $host = 'localhost';
        $user = 'APopovcev';
        $password = '3NYD-a78C9h78@Y';
        $db_name = 'sght_schedule';

        $link = mysqli_connect($host, $user, $password, $db_name)
        or die("Error connection");
        
        $query = 'SELECT Id, shortName 
                  FROM sgroup 
                  WHERE coursStudy = ' . $course;

        $result = mysqli_query($link, $query) 
            or die(mysqli_error($link));

        $data = array();
        while ($row = mysqli_fetch_assoc($result))
        $data[] = $row;

        if (!empty($data)) {
            header('Content-Type: application/json');
            $json = json_encode($data);
            echo '{ "data" : ' .  $json . ' }';
        }
    }
}

?>