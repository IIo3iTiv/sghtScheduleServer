<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if(isset($_GET["date"])) $date = htmlentities($_GET["date"]);
    if(isset($_GET["teacher"])) $teacher = htmlentities($_GET["teacher"]);

    $isEmpty = false;
    if(empty($teacher) || empty($date)) $isEmpty = true;

    if(!$isEmpty) {

        $host = 'localhost';
        $user = 'APopovcev';
        $password = '3NYD-a78C9h78@Y';
        $db_name = 'sght_schedule';

        $link = mysqli_connect($host, $user, $password, $db_name)
        or die("Error connection");
        
        $query = "SELECT a.date, a.numPair, TIME_FORMAT(g.timeStartLesson, '%H:%i') AS 'timeStart', TIME_FORMAT(g.timeEndLesson, '%H:%i') AS 'timeEnd', TIME_FORMAT(g.timeBreak, '%i') AS 'timeBreak', b.shortName AS 'group', b.coursStudy, c.compName AS 'teacher', d.name AS 'discipline', e.name AS 'auditorium', f.num AS 'subgroup'
        FROM timetable AS a 
        JOIN sgroup AS b ON a.idGroup = b.Id 
        JOIN teachers AS c ON a.idTeacher = c.Id 
        JOIN discipline AS d ON a.idDiscipline = d.Id 
        JOIN auditorium AS e ON a.idAuditorium = e.Id 
        JOIN subgroup AS f ON a.subGroup = f.num
        JOIN timepattern AS g ON a.numPair = g.numPair 
        WHERE 1 = 1
        and c.Id = " . $teacher . "
        and a.date = '" . $date . "'
        ORDER BY a.numPair, a.date ";

/*

SELECT a.date, a.numPair, TIME_FORMAT(g.timeStartLesson, '%H:%i') AS 'timeStart', TIME_FORMAT(g.timeEndLesson, '%H:%i') AS 'timeEnd', TIME_FORMAT(g.timeBreak, '%i') AS 'timeBreak', b.shortName AS 'group', b.coursStudy, c.compName AS 'teacher', d.name AS 'discipline', e.name AS 'auditorium', f.num AS 'subgroup' 
FROM timetable AS a 
JOIN sgroup AS b ON a.idGroup = b.Id 
JOIN teachers AS c ON a.idTeacher = c.Id 
JOIN discipline AS d ON a.idDiscipline = d.Id 
JOIN auditorium AS e ON a.idAuditorium = e.Id 
JOIN subgroup AS f ON a.subGroup = f.num 
JOIN timepattern AS g ON a.numPair = g.numPair 
WHERE 1 = 1 
and c.Id = 3 
and a.date = '2021-02-15' 
ORDER BY a.numPair, a.date

*/

        $result = mysqli_query($link, $query) 
            or die(mysqli_error($link));

        $data = array();
        while ($row = mysqli_fetch_assoc($result))
        $data[] = $row;

        header('Content-Type: application/json');
        $json = json_encode($data);
        echo '{ "data" : ' .  $json . ' }';
    }

}

?>