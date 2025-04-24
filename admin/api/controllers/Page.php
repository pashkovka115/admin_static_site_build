<?php

namespace Controllers;

use Engine\Constant;
use Engine\EditHTML;
use Engine\File;
use Engine\Header;
use Lib\DiDom\Document;
use Lib\DiDom\Element;

class Page
{
    private string $base_href;
    private string $temp_dir;


    public function __construct()
    {
        $this->base_href = Constant::BaseHref();
        $this->temp_dir = dirname(__DIR__) . '/temp/';
    }

    public function index()
    {
        $f = new File();

        echo json_encode($f->listHTML());
    }

    public function normalizeLinks()
    {
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
            $f = new File();
            $html = $f->getFileAsStr($page);
            $this->savePageToBackup($page, $html);

            $editorHTML = new EditHTML();
            $new_html = $editorHTML->normalizeLinks($html);
            $new_html = $editorHTML->addTagBase($new_html);

            $f->writeFile($page, $new_html);
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }

    /*
     * Сборщик может очищать выходную папку
     * надо переключить emptyOutDir: false
     */
    public function savePageToBackup($page = false, $html = false)
    {
        $_POST = json_decode(file_get_contents('php://input'), true);

        if (isset($_POST['page'])) {
            $file = $_POST['page'];
        }
        if (isset($_POST['html'])) {
            $new_html = $_POST['html'];
        }

        if ($page) {
            $file = $page;
        }

        if ($html) {
            $new_html = $html;
        }
        if (!isset($file)) {
            exit('Не указан файл для сохранения!');
        }

        $dir_backup = '../backups';

        if (!is_dir($dir_backup)) {
            mkdir($dir_backup);
        }

        $backups = [];
        if (file_exists($dir_backup . '/backup.json')) {
            $backups = json_decode(file_get_contents($dir_backup . '/backup.json'), true);
        }

        if ($new_html && $file) {
            $backupFN = '/' . uniqid() . '.html';
            copy('../../' . $file, $dir_backup . $backupFN);
            $backups[] = ['page' => basename($file), 'file' => $backupFN, 'time' => date('H:i:s d-m-Y')];
            file_put_contents($dir_backup . '/backup.json', json_encode($backups, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));

            file_put_contents('../../' . $file, $new_html);
        } else {
            header('HTTP/1.0 400 Bad Request');
        }

    }

    /*protected function wrapHTML(string $html): string
    {
        $editHTML = new EditHTML();
        $html = $editHTML->wrapTextNodes($html);
        $html = $editHTML->wrapImages($html);
        $html = $editHTML->injectStyle($html);
        $html = $editHTML->addEasyCmsPanel($html);

        return $html;
    }*/

    /*protected function unWrapHTML(string $html): string
    {
        $editHTML = new EditHTML();
        $html = $editHTML->unWrapTextNodes($html);
        $html = $editHTML->unWrapImages($html);
        $html = $editHTML->unInjectStyle($html);
        $html = $editHTML->deleteEasyCmsPanel($html);

        return $html;
    }*/

    public function getPage($page = false, $return = false)
    {
        if (isset($_REQUEST['page'])) {
            $page = $_REQUEST['page'];
        }
        $f = new File();
        $editHTML = new EditHTML();

        $html = $f->getFileAsStr($page);
        if ($editHTML->isValidHTML($html)) {
            $html = $editHTML->wrapHTML($html);
        }

        if ($return) {
            return $html;
        }
        echo $html;

    }

    public function saveNodesFromPage()
    {
        if (isset($_REQUEST['nodes'])) {

            $data = json_decode($_REQUEST['nodes'], true);
            $page = $data['page'];
            if (!$page) return;
            $nodes = $data['nodes'];
            if (!$nodes) return;

            $f = new File();
            $editor = new EditHTML();
            $html_not_nodes = $f->getFileAsStr($page);

            if ($editor->isValidHTML($html_not_nodes)) {
                $this->savePageToBackup($page, $html_not_nodes);
            } else {
                echo json_encode(['error' => 'Файл на сервере не валиден']);
                return;
            }

            $html_with_nodes = $this->getPage($page, true);

            if ($editor->isValidHTML($html_with_nodes)) {
                $html_with_nodes = $editor->changeTextNodes($html_with_nodes, $nodes);

                $changed_html = $editor->unWrapHTML($html_with_nodes);
                $f->writeFile($page, $changed_html);

                echo json_encode(['success' => 'Данные сохранены.']);
            } else {
                echo json_encode(['error' => 'Страница на сервере не валидна']);
                return;
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }

    /*public function save()
    {
        // todo: savePageToBackup
        if (isset($_REQUEST['html']) && isset($_REQUEST['page'])) {
            $f = new File();
            $f->writeFile($_REQUEST['page'], $_REQUEST['html']);
            echo json_encode([$_REQUEST['page'], $_REQUEST['html']]);
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }*/

    /*public function saveTempPage()
    {
        if (isset($_REQUEST['html'])) {
            if (!is_dir($this->temp_dir)){
                mkdir($this->temp_dir);
            }
            $f = new File();
            $f->writeFile($this->temp_dir . Constant::TmpFile(), $_REQUEST['html'], true);
        } else {
            header('HTTP/1.0 400 Bad Request');
        }
    }*/

    /*public function getTempPage()
    {
        Header::noCach();

        $f = new File();
        $tmp_file = $f->getFileAsStr($this->temp_dir . Constant::TmpFile(), true);

        if ($tmp_file) {
            echo $tmp_file;
        }else{
            echo "null" . (string)$tmp_file;
        }
    }*/

    /*public function deleteTempPage()
    {
        $f = new File();
        $f->delete($this->temp_dir . Constant::TmpFile(), true);
    }*/
}
