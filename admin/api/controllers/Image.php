<?php

namespace Controllers;

use Engine\EditHTML;
use Engine\File;
use Lib\DiDom\Document;

class Image
{
    public function upload()
    {
        if (!is_dir('../img/')) {
            mkdir('../img/');
        }

        if (file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {

//            file_put_contents('editableimageid.txt', $_REQUEST['editableimageid']);

            $file_name = uniqid() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../../img/' . $file_name);

            $src = '/img/' . $file_name;

            if (isset($_REQUEST['editableimageid']) && is_numeric($_REQUEST['editableimageid'])){
                if (isset($_REQUEST['page']) && $_REQUEST['page']){
                    $page_name = $_REQUEST['page'];
                    $html_from_page = $this->getPage($page_name);

                    if ($html_from_page){
                        $id = $_REQUEST['editableimageid'];
                        $document = new Document($html_from_page);
                        $document->first("[editableimageid=$id]")->setAttribute('src', $src);

                        $html_with_nodes = $document->html();

                        $f = new File();
                        $editor = new EditHTML();

                        $changed_html = $editor->unWrapHTML($html_with_nodes);
                        $f->writeFile($page_name, $changed_html);

                        echo json_encode(['src' => $src, 'success'=>'Данные сохранены.']);
                    }
                }else{
                    echo json_encode(['error' => 'Не известная страница']);
                }
            }else{
                echo json_encode(['error' => 'Не известный идентификатор картинки', 'param' => $_REQUEST['editableimageid']]);
            }
        } else {
            echo json_encode(['error' => 'Не известная ошибка при загрузке файла!']);
        }
    }


    protected function getPage($page = false)
    {
        $f = new File();
        $editHTML = new EditHTML();

        $html = $f->getFileAsStr($page);
        if ($editHTML->isValidHTML($html)) {
            $html = $editHTML->wrapHTML($html);
        }else{
            $html = false;
        }

        return $html;
    }
}