<?php

class File
{
    /**
     * Путь к редактируемым файлам. Корневая директория.
     */
    const PATH_DOCUMENT_ROOT = '../../../';

    /**
     * @param bool $full_path Указан полный путь или только имя файла. По умолчанию только имя файла.
     * @return array|false
     */
    public function listHTML(bool $full_path = false)
    {
        $prepend_files = [];
        $files = glob(self::PATH_DOCUMENT_ROOT . '*.html');

        if (!$full_path){
            foreach ($files as $file){
                $prepend_files[] = basename($file);
            }
            return $prepend_files;
        }
        return $files;
    }

    public function getFileAsStr(string $file, bool $full_path = false)
    {
        if (!$full_path){
            if (file_exists(self::PATH_DOCUMENT_ROOT . $file)){
                return file_get_contents(self::PATH_DOCUMENT_ROOT . $file);
            }
        }else{
            if (file_exists($file)){
                return file_get_contents($file);
            }
        }

        return false;
    }

    public function writeFile(string $file, string $data, bool $full_path = false)
    {
        if (!$full_path){
            return file_put_contents(self::PATH_DOCUMENT_ROOT . $file, $data);
        }
        return file_put_contents($file, $data);
    }

    public function writeFileIfNotExists(string $file, string $data, bool $full_path = false)
    {
        if (!$full_path && !file_exists(self::PATH_DOCUMENT_ROOT . $file)){
            return file_put_contents(self::PATH_DOCUMENT_ROOT . $file, $data);
        }elseif ($full_path && !file_exists($file)){
            return file_put_contents($file, $data);
        }
        return false;
    }
}
