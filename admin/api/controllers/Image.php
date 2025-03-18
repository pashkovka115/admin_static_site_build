<?php

namespace Controllers;

class Image
{
    public function upload()
    {
        if (!is_dir('../img/')) {
            mkdir('../img/');
        }

// моя весия кода
        if (file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
            $file_name = uniqid() . '_' . $_FILES['image']['name'];

            move_uploaded_file($_FILES['image']['tmp_name'], '../../img/' . $file_name);

            echo json_encode(['src' => $file_name]);
        } else {
            echo json_encode(['error' => 'Не известная ошибка при загрузке файла!']);
        }

// Оригинальная версия кода
        /*if (file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])){
            $file_ext = explode('/', $_FILES['image']['type'])[1];
            $file_name = uniqid() . '_' . $_FILES['image']['name'] . '.' . $file_ext;

            move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $file_name);
        }*/

    }
}