<?php
class GenerateSiteMap1e{
    private $file = null;
    private $urls = array();
    private $xmlns = "http://www.sitemaps.org/schemas/sitemap/0.9";

    public function __construct($route = null)
    {
        if(is_null($route)){
            $route = dirname(dirname(__FILE__)) . "/sitemap.xml";
        }
        $this->open($route);
    }

    public function getXmlns(){
        return $this->xmlns;
    }

    public function setXmlns($xmlns){
        $this->xmlns = $xmlns;
    }
    
    private function open($route){
        //Obtenemos el contenido
        $xml = file_get_contents($route);
        if($xml != "" && $xml !== false){
            $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $this->urls = json_decode($json,TRUE)["url"];
        }

        $this->file = fopen($route, "w+");
        if($this->file === false){
            throw new Exception('No pudimos abrir el archivo: ' . $route);
        }
    }

    public function addUrl($loc, $lastmod = null){
        if($lastmod == null){
            $lastmod = date('Y-m-d');
        }
        $this->urls[] = array(
            'loc' => $loc,
            'lastmod' => $lastmod
        );
    }

    public function updateUrl($previousLoc, $nextLoc){
        $key = array_search($previousLoc, array_column($this->urls, 'loc'));
        if($key !== false){
            $this->urls[$key] = array(
                'loc' => $nextLoc,
                'lastmod' => date('Y-m-d')
            );
        }
    }

    public function deleteUrl($loc){
        $key = array_search($loc, array_column($this->urls, 'loc'));
        unset($this->urls[$key]);
    }

    public function save(){
        $header = '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="' . $this->xmlns . '">';
        $body = '';
        foreach ($this->urls as $url) {
            $body .= '<url>';
            foreach ($url as $key => $value) {
                $body .= '<' . $key . '>' . $value . '</' . $key . '>';
            }
            $body .= '</url>';
        }
        $footer = '</urlset>';
        fwrite($this->file, $header . $body . $footer);
        $this->close();
    }

    private function close(){
        fclose($this->file);
    }
}
