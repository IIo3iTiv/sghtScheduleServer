<?

# Подключаем библиотеку PHPExcel
require_once '../../module/PHPExcel.php';

# Загружаем файл с расписанием в объект
$validLocate = PHPExcel_Settings::setLocale('ru');
$excel = PHPExcel_IOFactory::load('../../files/plan.xlsx');

# Считываем книгу в массив $tables
$inputFileName = '../../files/plan.xlsx';
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

foreach ($tables as $numSheet => $table) {
    echo '<style>
        TABLE {
            border-collapse: collapse; 
            border: 1px solid #000; 
        }
        TD, TH {
            padding: 5px; 
            border: 1px solid #000;
        }
    </style>';
    echo '<h3>' . $numSheet . '</h3>';
    foreach ($table as $nRow => $row) {
        echo '<table><tbody>' . $nRow . '<tr>';
        foreach ($row as $nCol => $col) {
            echo '<td>' . $col;
            if ($col == null) echo '!!!';
            echo '</td>';
        }
        echo '</tr>';
    }
}


?>