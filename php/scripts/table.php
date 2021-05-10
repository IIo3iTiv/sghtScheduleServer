<?

# Устанавливаем доступ к БД
$host = 'localhost';
$user = 'APopovcev';
$password = '3NYD-a78C9h78@Y';
$db_name = 'sght_schedule';

$link = mysqli_connect($host, $user, $password, $db_name)  or die("Error connection"); 

$query = 'SELECT a.date, g.name AS dayWeek, b.shortName, c.compName, d.name AS discipline, e.name AS auditorium, a.numPair, a.subGroup
          FROM timetable AS a
             , sgroup AS b
             , teachers AS c 
             , discipline AS d 
             , auditorium AS e
             , dayweek AS g 
          WHERE 1 = 1 
             AND a.idGroup = b.Id 
             AND a.idTeacher = c.Id 
             AND a.idDiscipline = d.Id 
             AND a.idAuditorium = e.Id 
             AND a.dayWeek = g.numDay '
;

$result = mysqli_query($link, $query) or die(mysqli_error($link));
for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

echo '
<style type="text/css">
.table {
	width: 100%;
	margin-bottom: 20px;
	border: 1px solid #dddddd;
	border-collapse: collapse; 
}

.table th {
	font-weight: bold;
	padding: 5px;
	background: #efefef;
	border: 1px solid #dddddd;
}

.table td {
	border: 1px solid #dddddd;
	padding: 5px;
} 
</style>';

echo '
<table class="table">
<thead>
    <tr>
        <th>Дата</th>
        <th>День недели</th>
        <th>Номер пары</th>
        <th>Группа</th>
        <th>Дисциплина</th>
        <th>Преподаватель</th>
        <th>Аудитория</th>
        <th>Подгруппа</th>
    </tr>
</thead>
<tbody>';

foreach ($data as $j => $pair) {
    echo '<td>' . $pair['date'] . '</td>';
    echo '<td>' . $pair['dayWeek'] . '</td>';
    echo '<td>' . $pair['numPair'] . '</td>';
    echo '<td>' . $pair['shortName'] . '</td>';
    echo '<td>' . $pair['discipline'] . '</td>';
    echo '<td>' . $pair['compName'] . '</td>';
    echo '<td>' . $pair['auditorium'] . '</td>';
    echo '<td>' . $pair['subGroup'] . '</td>';
    echo '</tr>';
}

echo '</tbody></table>';

?>