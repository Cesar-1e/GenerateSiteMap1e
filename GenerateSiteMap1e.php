<?php
class GenerateSiteMap1e
{
    private $file = null;
    private $urls = array();
    private $xmlns = "http://www.sitemaps.org/schemas/sitemap/0.9";

    public function __construct($route = null)
    {
        if (is_null($route)) {
            $route = dirname(dirname(__FILE__)) . "/sitemap.xml";
        }
        $this->open($route);
    }

    public function getXmlns()
    {
        return $this->xmlns;
    }

    public function setXmlns($xmlns)
    {
        $this->xmlns = $xmlns;
    }

    private function open($route)
    {
        if (!file_exists($route)) {
            $this->file = fopen($route, "w+");
            if ($this->file === false) {
                throw new Exception('No pudimos abrir el archivo: ' . $route);
            }
            return;
        }

        $xml = file_get_contents($route);
        if ($xml !== false && !empty($xml)) {
            $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);
            if ($xml === false) {
                throw new Exception('Error al cargar el XML: ' . $route);
            }
            $json = json_encode($xml);
            $this->urls = json_decode($json, TRUE)["url"];
        }

        $this->file = fopen($route, "w+");
        if ($this->file === false) {
            throw new Exception('No pudimos abrir el archivo: ' . $route);
        }
    }

    public function addUrl($loc, $lastmod = null)
    {
        if (!filter_var($loc, FILTER_VALIDATE_URL)) {
            throw new Exception('URL no válida: ' . $loc);
        }

        if ($lastmod == null) {
            $lastmod = date('Y-m-d');
        }

        $this->urls[] = array(
            'loc' => $loc,
            'lastmod' => $lastmod
        );
    }

    public function updateUrl($previousLoc, $nextLoc)
    {
        if (!filter_var($nextLoc, FILTER_VALIDATE_URL)) {
            throw new Exception('URL no válida: ' . $nextLoc);
        }

        $key = array_search($previousLoc, array_column($this->urls, 'loc'));
        if ($key !== false) {
            $this->urls[$key] = array(
                'loc' => $nextLoc,
                'lastmod' => date('Y-m-d')
            );
        }
    }

    public function deleteUrl($loc)
    {
        $key = array_search($loc, array_column($this->urls, 'loc'));
        if ($key !== false) {
            unset($this->urls[$key]);
            $this->urls = array_values($this->urls); // Reindex array
        }
    }

    public function save()
    {
        $header = '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="' . $this->xmlns . '">';
        $body = '';
        foreach ($this->urls as $url) {
            $body .= '<url>';
            foreach ($url as $key => $value) {
                $body .= '<' . $key . '>' . htmlspecialchars($value, ENT_XML1, 'UTF-8') . '</' . $key . '>';
            }
            $body .= '</url>';
        }
        $footer = '</urlset>';
        fwrite($this->file, $header . $body . $footer);
        $this->close();
    }

    private function close()
    {
        if ($this->file !== null) {
            fclose($this->file);
        }
    }
}
