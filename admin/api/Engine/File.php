<?php
namespace Engine;

class File
{
    /**
     * Путь к редактируемым файлам. Корневая директория.
     */

    public static function path_document_root()
    {
        return realpath(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param bool $full_path Указан полный путь или только имя файла. По умолчанию только имя файла.
     * @return array|false
     */
    public function listHTML(bool $full_path = false)
    {
        $prepend_files = [];
        $files = glob(self::path_document_root() . '[!_-]*.html');

        if (!$full_path){
            foreach ($files as $file){
                if (basename($file) == 'editorfile.html'){
                    continue;
                }
                $prepend_files[] = basename($file);
            }
            return $prepend_files;
        }
        return $files;
    }

    public function getFileAsStr(string $file, bool $full_path = false)
    {
        if ($file) {
            if (!$full_path) {
                $file = self::path_document_root() . $file;
            }

            if (file_exists($file)) {
                return file_get_contents($file);
            } else {
                return "false $file";
            }
        } else {
            return "false $file";
        }
    }

    public function writeFile(string $file, string $data, bool $full_path = false)
    {
        if (!$full_path){
            return file_put_contents(self::path_document_root() . $file, $data);
        }
        return file_put_contents($file, $data);
    }

    public function writeFileIfNotExists(string $file, string $data, bool $full_path = false)
    {
        if (!$full_path && !file_exists(self::path_document_root() . $file)){
            return file_put_contents(self::path_document_root() . $file, $data);
        }elseif ($full_path && !file_exists($file)){
            return file_put_contents($file, $data);
        }
        return false;
    }

    public function delete(string $file, bool $full_path = false)
    {
        if (!$full_path){
            $file = self::path_document_root() . $file;
        }
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
