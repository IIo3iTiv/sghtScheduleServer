<?php

# Устанавливаем доступ к БД
$host = 'localhost';
$user = 'APopovcev';
$password = '3NYD-a78C9h78@Y';
$db_name = 'sght_schedule';

$link = mysqli_connect($host, $user, $password, $db_name)  or die("Error connection"); 

# ../../../files/data.json
# ../../files/data.json

# Читаем data.json
$string = file_get_contents('../../../files/data.json');

# Превращаем в объект
$data = json_decode($string);

# Отлавливаем ошибки возникшие при превращении
switch (json_last_error()) {
    case JSON_ERROR_NONE:
      $data_error = '';
    break;

    case JSON_ERROR_DEPTH:
      $data_error = 'Достигнута максимальная глубина стека';
    break;

    case JSON_ERROR_STATE_MISMATCH:
      $data_error = 'Неверный или не корректный JSON';
    break;

    case JSON_ERROR_CTRL_CHAR:
      $data_error = 'Ошибка управляющего символа, возможно верная кодировка';
    break;

    case JSON_ERROR_SYNTAX:
      $data_error = 'Синтаксическая ошибка';
    break;

    case JSON_ERROR_UTF8:
      $data_error = 'Некорректные символы UTF-8, возможно неверная кодировка';
    break; 

    default:
      $data_error = 'Неизвестная ошибка';
    break;
}

# Если ошибки есть, то выводим их
if($data_error != '') {
  echo $data_error;
  return;
}

# Просматриваем данные и заносим их в БД
foreach ($data as $i => $pair) {

    # Получаем данные из объекта
    $date = $pair->date;
    $dayWeek = $pair->dayWeek;
    $coursStudy = $pair->coursStudy;
    $shortName = $pair->shortName;
    $fullName = $pair->fullName;
    $specCode = $pair->specCode;
    $numPair = $pair->numPair;
    $subGroup = $pair->subGroup;
    $auditorium = $pair->auditorium;
    $discipline = $pair->discipline;
    $teacher = $pair->teacher;

    /*
        Нужно проверить на существование:
        1. Группа
        2. Аудитория
        3. Преподователь
        4. Дисциплина
    */

    # Группа
    $query = 'SELECT *
              FROM sgroup AS g
              WHERE 1 = 1
                and g.specCode = "' . $specCode . '"
                and g.shortName = "' . $shortName . '"'
    ;
    
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    if (count($data) == 0) {
        $query = 'INSERT INTO sgroup 
                  SET shortName = "' . $shortName . '"
                    , fullName = "' . $fullName . '"
                    , specCode = "' . $specCode . '"
                    , coursStudy = "' . $coursStudy . '"'
        ;

        mysqli_query($link, $query) or die(mysqli_error($link));
    }

    # Аудитория
    $query = 'SELECT *
              FROM auditorium
              WHERE name = "' . $auditorium . '"'
    ;

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    if (count($data) == 0) {
        $query = 'INSERT INTO auditorium 
                  SET name = "' . $auditorium . '"'
        ;

        mysqli_query($link, $query) or die(mysqli_error($link));
    }

    # Преподаватель
    $query = 'SELECT *
              FROM teachers 
              WHERE compName = "' . $teacher . '"'
    ;

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    if (count($data) == 0) {
        $query = 'INSERT INTO teachers 
                  SET compName = "' . $teacher . '"'
        ;

        mysqli_query($link, $query) or die(mysqli_error($link));
    }

    # Дисциплина
    $query = 'SELECT *
              FROM discipline 
              WHERE name = "' . $discipline . '"'
    ;

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    if (count($data) == 0) {
        $query = 'INSERT INTO discipline 
                  SET name = "' . $discipline . '"'
        ;

        mysqli_query($link, $query) or die(mysqli_error($link));
    }

    # Получаем id Группы
    $query = 'SELECT Id
              FROM sgroup 
              WHERE specCode = "' . $specCode . '"
              and shortName = "' . $shortName . '"'
    ;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    $idGroup = $data[0]["Id"];

    # Получаем id Преподавателя
    $query = 'SELECT Id
              FROM teachers 
              WHERE compName = "' . $teacher . '"'
    ;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    $idTeacher = $data[0]["Id"];

    # Получаем id Дисциплины
    $query = 'SELECT Id
              FROM discipline 
              WHERE name = "' . $discipline . '"'
    ;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    $idDiscipline = $data[0]["Id"];

    # Получаем id Аудитории
    $query = 'SELECT Id
              FROM auditorium 
              WHERE name = "' . $auditorium . '"'
    ;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);
    $idAuditorium  = $data[0]["Id"];

    # Добавляем пару
    $query = 'SELECT * 
              FROM timetable
              WHERE 1 = 1
                and date = "' . $date . '" 
                and dayWeek = ' . $dayWeek . '
                and idGroup = ' . $idGroup . '
                and idTeacher = ' . $idTeacher . ' 
                and idDiscipline = ' . $idDiscipline . '
                and idAuditorium = ' . $idAuditorium . '
                and numPair = ' . $numPair . ' 
                and subGroup = '. $subGroup
    ;

    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row);

    if (count($data) == 0) {
        $query = 'INSERT INTO timetable 
                  SET date = "' . $date . '"
                    , dayWeek = "' . $dayWeek . '"
                    , idGroup = "' . $idGroup . '"
                    , idTeacher = "' . $idTeacher . '"
                    , idDiscipline = "' . $idDiscipline . '"
                    , idAuditorium = "' . $idAuditorium . '"
                    , numPair = "' . $numPair . '"
                    , subGroup = "'. $subGroup . '"'
        ;

        echo $i;
        mysqli_query($link, $query) or die(mysqli_error($link));
        echo ' - номана<br>';
    }

}

?>