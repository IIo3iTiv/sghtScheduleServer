<?php

# Подключаем библиотеку PHPExcel
require_once '../../../module/PHPExcel.php';

# Загружаем файл с расписанием в объект
$validLocate = PHPExcel_Settings::setLocale('ru');
$excel = PHPExcel_IOFactory::load('../../../files/plan.xlsx');

# Считываем книгу в массив $tables
$inputFileName = '../../../files/plan.xlsx';
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objExcel = $objReader -> load($inputFileName);
} catch (Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
}

# Считываем все листы
foreach ($objExcel -> getWorksheetIterator() as $worksheet) {
    $tables[] = $worksheet -> toArray();
}

$date; # Дата пары
$dayWeek; # День недели
$numPair; # Номер пары
$arrMonth = array( "01" => "января", "02" => "февраля", "03" => "марта", "04" => "апреля", "05" => "мая", "06" => "июня", "07" => "июля", "08" => "августа", "09" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декбря");
$arrWeek = array("понедельник", "вторник", "среда", "четверг", "пятница", "суббота", "воскресенье");
$arrGroup = array(); # Массив групп
$schedule = array(); # Массив расписания
$subGen = '3 подгр.'; # Общая пара
$subGer = '4 подгр.'; # Немецкая группа
$subEng = '5 подгр.'; # Английская группа


foreach ($tables as $nTable => $table) {
    foreach ($table as $nRow => $row) {

        # Информация о группах
        if (preg_match('/\d{1}(курс)|(звонков)|(перемена)/', implode(' ', $row))) {
            foreach ($table[$nRow+1] as $nCol => $col) {
                if (!empty($col)) {
                    $colGroup = $nCol;
                    $string = $col;
                    preg_match('/\d{2}.\d{2}.\d{2}/', $string, $res);
                    $specCode = $res[0];
                    $string = preg_replace('/\d{2}.\d{2}.\d{2}/', '', $string);
                    $string = preg_replace('/(группа)/', '-', $string);
                    $string = preg_split('/-/', $string);
                    $fullName = $string[0];
                    $shortName = $string[1];
                    $coursStudy = $string[2];
                    array_push($arrGroup, array(
                            "nColGroup" => $nCol # Номер колонки в которой находится группа
                            ,"coursStudy" => $coursStudy
                            ,"specCode" => $specCode
                            ,"fullName" => trim($fullName)
                            ,"shortName" => trim($shortName)
                        )
                    );
                }
            }
        }

        # Дата. Если нашли дату, то формируем расписание
        if (preg_match('/\d/', $row[2])) {
            $string = str_replace(' ', '', $row[2]);
            foreach ($arrWeek as $nDay => $day) {
                if (preg_match('/(' . $day . ')/', $string)) {
                    $string = preg_replace('/(' . $day . ')/', ' ', $string);
                    foreach ($arrMonth as $nMonth => $month) {
                        if (preg_match('/(' . $month . ')/', $string)) {
                            preg_match('/\d*/', $string, $res);
                            $date = $res[0];
                            $date = date('Y') . '.' . $nMonth . '.' . $date;
                            $dayWeek = $nDay + 1;
                            $nDate = $nRow;
                            break 2;
                        }
                    }
                }
            }

            # Расписание
            foreach ($arrGroup as $nGroup => $group) {
                $i = $group["nColGroup"]; # Колонка
                $j = $nDate; # Строка

                # Построчно проходим по парам
                for ($j; $j < $nDate + 8; $j++) {
                    $string = $table[$j][$i];
                    
                    # Пропускаем не нужное
                    if (empty($string) || trim($string) == '-' || empty($table[$j][$i+1])) continue;

                    # Вставляем 3 подгр.
                    if (preg_match('/^(?!.*(Немецкий|Английский|Англ\.|(\d{1} (подгр.)|\d{1}(подгр.))|Консультации))/', $string)) {
                        preg_match('/ [А-Яа-я]+ [А-Я]\.[А-Я]\./u', $string, $val);
                        $start = strpos($string, $val[0]);
                        $end = strlen($val[0]);
                        $string = substr_replace($string, $subGen . $val[0], $start, $end);
                    }

                    # Вставляем 4 подгр.
                    if (preg_match('/Немецкий/', $string)) {
                        $start = strpos($string, 'Немецкий язык');
                        $end = strlen('Немецкий язык ');
                        $string = substr_replace($string, 'Немецкий язык ' . $subGer, $start, $end);
                    }

                    # Вставляем 5 подгр.
                    if (preg_match('/Английский|Англ\./', $string)) {
                        $start = strpos($string, 'Английский язык');
                        $end = strlen('Английский язык ');
                        $string = substr_replace($string, 'Английский язык ' . $subEng, $start, $end);
                    }

                    # Номер пары
                    $numPair = trim($table[$j][3]);

                    # Преподователи
                    preg_match_all('/ [А-Яа-я]+ [А-Я]\.[А-Я]\./u', $string, $arrTeacher);
                    $string = preg_replace('/ [А-Яа-я]+ [А-Я]\.[А-Я]\./u', '', $string);

                    # Подгруппа
                    preg_match_all('/\d{1} ?подгр./', $string, $subGroup);
                    foreach ($subGroup[0] as $k => $val)
                        $subGroup[0][$k] = preg_replace('/\D/', '', $val);
                    $string = preg_replace('/\d{1} ?подгр./', '@', $string);

                    # Дисциплины
                    $arrDiscipline = preg_split('/@/', $string);
                    array_pop($arrDiscipline);

                    #Аудитории
                    $string = $table[$j][$i+1];
                    preg_match_all('/(\d+|М|Мастер(-?)ские|Библио(-?)тека|Сам(.*?)раб|Лыжи|Акт(.*?)зал)|Спор(.*?)зал/iu', $string, $arrAuditorium);
                    
                    # Формируем массив
                    foreach ($arrDiscipline as $k => $val) {
                        array_push($schedule, array(
                            "date" => $date
                            ,"dayWeek" => $dayWeek
                            ,"coursStudy" => $group["coursStudy"]
                            ,"shortName" => $group["shortName"]
                            ,"fullName" => $group["fullName"]
                            ,"specCode" => $group["specCode"]
                            ,"numPair" => $numPair[0]
                            ,"subGroup" => $subGroup[0][$k]
                            ,"auditorium" => trim($arrAuditorium[0][$k])
                            ,"discipline" => trim($arrDiscipline[$k])
                            ,"teacher" => trim($arrTeacher[0][$k])
                        ));
                    }
                }
            }
        }
    }
}

# Формируем JSON файл
$json = json_encode($schedule, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$file = fopen('../../../files/data.json','w+');
fwrite($file, $json);
fclose($file);

?>