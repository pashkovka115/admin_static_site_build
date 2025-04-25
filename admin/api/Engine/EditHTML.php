<?php

namespace Engine;

use Lib\DiDom\Document;
use Lib\DiDom\Element;
use Lib\DiDom\Node;

class EditHTML
{
    private string $base_href;

    public function __construct()
    {
        $this->base_href = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
    }


    public function setMeta(string $html, string $title, string $keywords, string $description): string
    {
        $document = new Document($html);

        if ($document->has('title')){
            $document->first('head title')->setValue($title);
        }else{
            $title = new Element('title', $title);
            $document->first('head')->appendChild($title);
        }

        if ($document->has('meta[name=keywords]')){
            $document->first('meta[name=keywords]')->setAttribute('content', $keywords);
        }else{
            $keywords = new Element('meta', '', ['name' => 'keywords', 'content' => $keywords]);
            $document->first('head')->appendChild($keywords);
        }

        if ($document->has('meta[name=description]')){
            $document->first('meta[name=description]')->setAttribute('content', $description);
        }else{
            $description = new Element('meta', '', ['name' => 'keywords', 'content' => $description]);
            $document->first('head')->appendChild($description);
        }

        return $document->html();
    }



    public function wrapHTML(string $html): string
    {
        $html = $this->wrapTextNodes($html);
        $html = $this->wrapImages($html);
        $html = $this->injectStyle($html);
        $html = $this->addEasyCmsPanel($html);

        return $html;
    }

    public function unWrapHTML(string $html): string
    {
        $html = $this->unWrapTextNodes($html);
        $html = $this->unWrapImages($html);
        $html = $this->unInjectStyle($html);
        $html = $this->deleteEasyCmsPanel($html);

        return $html;
    }

    public function changeTextNodes(string $html, array $nodes): string
    {
        $document = new Document($html);

        foreach ($nodes as $node) {
            $child = $document->first("text-editor[nodeid={$node['nodeid']}]");
            $child->setValue($node['text']);
        }

        return $document->html();
    }

    public function isValidHTML(string $html): bool
    {
        if (
            str_contains($html, '<html')
            && str_contains($html, '<head>')
            && str_contains($html, '<title>')
            && str_contains($html, '</title>')
            && str_contains($html, '</head>')
            && str_contains($html, '<body')
            && str_contains($html, '</body>')
            && str_contains($html, '</html>')
        ) {
            return true;
        }
        return false;
    }


    public function wrapTextNodes(string $html): string
    {
        $document = new Document($html);
        $body = $document->first('body');

        function wrap_text($children, $i = 0)
        {
            static $i;
            /**
             * @var $child Node
             */
            foreach ($children as $child) {
                if (count($child->children()) == 0) {
                    if ($child->isTextNode()) { // \s+
                        $space = preg_replace("/\s+/", ' ', $child->getNode()->nodeValue);
                        if ($space == ' ') {
                            $space = '';
                        }
                        $child->setValue($space);

                        if (strlen($child->getNode()->nodeValue) > 0) {
                            $i++;
                            $txe = new Element('text-editor', $child->getNode()->nodeValue, ['nodeid' => $i, 'contenteditable' => "false"]);
                            $child->replace($txe);
                        }
                    }
                } else {
                    wrap_text($child->children(), $i);
                }
            }
        }

        wrap_text($body->children());


        return $document->html();
    }


    public function unWrapTextNodes(string $html): string
    {
        $document = new Document($html);
        $body = $document->first('body');

        function un_wrap_text($children)
        {
            /**
             * @var $child Node
             */
            foreach ($children as $child) {
                if ($child->tagName() == 'text-editor') {
                    $text = $child->innerHtml();
                    $text_node = $child->parent()->toDocument()->createTextNode($text);
                    $child->parent()->insertBefore($text_node);
                    $child->remove();
                } else {
                    un_wrap_text($child->children());
                }
            }
        }

        un_wrap_text($body->children());


        return $document->html();
    }

    public function wrapImages(string $html): string
    {
        $document = new Document($html);
        $images = $document->find('img');
        $i = 0;
        foreach ($images as $image) {
            $image->setAttribute('editableimageid', $i);
            $i++;
        }

        return $document->html();
    }

    public function unWrapImages(string $html): string
    {
        $document = new Document($html);
        $images = $document->find('img');

        foreach ($images as $image) {
            $image->removeAttribute('editableimageid');
        }

        return $document->html();
    }

    public function getFilesNamesForDevelopment()
    {
        $f = new File();
        $manifest = json_decode($f->getFileAsStr(Constant::BasePath() . 'admin/.vite/manifest.json', true), true);

        return [
            'js' => $manifest['index.html']['file'],
            'css' => $manifest['index.html']['css'][0],
        ];
    }


    public function addEasyCmsPanel(string $html): string
    {
        $document = new Document($html);
        $element_panel = new Element('div', '', ['id' => 'easycms_panel']);

        if (DEBUG) {
            $paths = $this->getFilesNamesForDevelopment();
            $path_to_css = '/admin/' . $paths['css'];
            $path_to_js = '/admin/' . $paths['js'];

            $element_script = new Element('script', '', ['type' => 'module', 'id' => 'easycms_script', 'src' => $path_to_js]);
            $element_style = new Element('link', '', ['id' => 'easycms_style', 'rel' => 'stylesheet', 'href' => $path_to_css]);
        } else {
            $element_script = new Element('script', '', ['type' => 'module', 'id' => 'easycms_script', 'src' => '/admin/assets/easycms.js']);
            $element_style = new Element('link', '', ['id' => 'easycms_style', 'rel' => 'stylesheet', 'href' => '/admin/assets/easycms.css']);
        }


        if ($document->has('head')) {
            $document->first('head')->appendChild($element_script);
            $document->first('head')->appendChild($element_style);
        }

        if ($document->has('body')) {
            $document->first('body')->prependChild($element_panel);
        }

        return $this->addTagBase($document->html());
    }

    public function deleteEasyCmsPanel(string $html): string
    {
        $document = new Document($html);

        if ($document->has('#easycms_panel')) {
            $document->first('#easycms_panel')->remove();
        }
        if ($document->has('#easycms_script')) {
            $document->first('#easycms_script')->remove();
        }
        if ($document->has('#easycms_style')) {
            $document->first('#easycms_style')->remove();
        }

        return $document->html();
    }

    public function addTagBase(string $html): string
    {
        $document = new Document($html);
        $element_base = new Element('base', '', ['href' => $this->base_href]);

        if ($document->has('base')) {
            $document->first('head base')->replace($element_base);
        } else {
            $document->first('head')->prependChild($element_base);
        }

        return $document->html();
    }


    public function normalizeLinks(string $html, array $attrs = ['href', 'src'])
    {
        $document = new Document($html);

        foreach ($attrs as $attr) {
            $els = $document->find("[$attr]");
            foreach ($els as $el) {
                $href = $el->getAttribute($attr);
                if (
                    !str_starts_with($href, 'http')
                    && $href != ''
                    && !str_starts_with($href, '#')
                    && !str_starts_with($href, '//')
                ) {
                    $href = $this->base_href . '/' . trim(trim($href, '.'), '/');
                    $el->setAttribute($attr, $href);
                }
            }
        }

        return $document->html();
    }

    public function injectStyle(string $html): string
    {
        $style = '
        text-editor:hover{
                outline: 3px solid orange !important;
                outline-offset: 8px !important;
            }
            text-editor:focus{
                outline: 3px solid red !important;
                outline-offset: 8px !important;
            }
            [editableimageid]:hover{
                outline: 3px solid orange !important;
                outline-offset: 8px !important;
            }
        ';
        $document = new Document($html);
        $element_style = new Element('style', $style, ['id' => 'easycms_style_for_text-editor']);

        if ($document->has('head')) {
            $document->first('head')->appendChild($element_style);
        }

        return $document->html();
    }

    public function unInjectStyle(string $html): string
    {
        $document = new Document($html);
        if ($document->has('#easycms_style_for_text-editor')) {
            $document->first('#easycms_style_for_text-editor')->remove();
        }

        return $document->html();
    }
}