<?php

require_once 'orbiter/Parser.php';

class Router {

    private $request_uri;

    function __construct() {
        $this->request_uri = explode('?', $_SERVER['REQUEST_URI'], 2)[0];
    }

    public function serve_current_path() {
        $config = include('config.php');

        if (strstr($this->request_uri, "..")) {
            // Disallow navigating into parent directories.
            $this->error_404();
        }

        if ($this->request_uri == '/') {
            $path = "/index.gmi";
        }
        else {
            $path = $this->request_uri;
        }

        $page = $config['content_dir'].$path;
        $extension = $this->get_file_extension($page);

        if ($extension == "gmi" || $extension == "gemini") {
            $this->parse_gmi_page($page);
        }
        else if ($extension == "php") {
            $this->error_404();
        }
        else {
            $this->serve_file($page);
        }
    }

    private function parse_gmi_page($page) {
        $parser = new Parser($page);
        
        if ($parser->page_exists()) {
            echo $parser->parse();
        }
        else {
            $this->error_404();
        }
    }

    private function serve_file($file) {
        if (file_exists($file)) {
            $mime_type = mime_content_type($file);
            header("Content-type: $mime_type");
            include($file);
        }
        else {
            $this->error_404();
        }
    }

    private function error_404() {
        include("./assets/error_404.html");
        http_response_code(404);
        exit();
    }

    private function get_file_extension($file) {
        $dot_split = explode('.', $file);
        return end($dot_split);
    }
}

