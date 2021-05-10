<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {

        $whiteList = array('xlsx'); # Допускаемые расширения
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); # Извлекаем расширение файла

        # Проверяем расширение файла
        if (in_array($extension, $whiteList)) {
            
            $file; # Новое название файла
            $folder = '../../../files/'; # Папка, куда будем загружать файл
            $upladFile = $folder . 'plan.' . $extension; # Путь до файла с уникальным именем

            # Загружаем файл
            move_uploaded_file($_FILES['file']['tmp_name'], $upladFile);

            # Читаем data.xlsx и формируем data.json
            require_once '../../scripts/read.php';
            
            # Возвращаемся на главную страницу
            echo '{"status":"success"}';
        }
        else {
            echo '{"status":"error1"}'; # Недопустимое расширение
        }
    }
    else {
        echo '{"status":"error2"}'; # Ошибка при загрузке файла
    }
}
else {
    echo '{"status":"error3"}'; # Ошибка при отправке запроса
} 

?>