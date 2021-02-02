<?php

class BlockTypes {
    const text = 0;
    const list = 1;
    const preformatted = 2;
}

class Parser {

    private $page_exists;
    private $file;
    private $block;
    private $output;
    private $title;

    function __construct($file) {
        if (file_exists($file)) {
            $this->page_exists = true;
            $this->file = fopen($file, 'r');
            $this->block = BlockTypes::text;
        } else {
            $this->page_exists = false;
        }
    }

    function __destruct() {
        if ($this->page_exists) {
            fclose($this->file);
        }
    }

    public function page_exists() {
        return $this->page_exists;
    }

    public function parse() {
        if (!$this->page_exists) {
            return false;
        }

        while(!feof($this->file)){
            $this->parse_line();    
        }
        return $this->wrap_output();
    }

    private function wrap_output() {
        $stylesheet = file_get_contents("./assets/style.css");
        return "<!DOCTYPE html>
<html>
<head>
<title>$this->title</title>
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<style>
$stylesheet
</style>
</head>
<body>
$this->output
</body>
</html>
";
    }

    private function parse_line() {
        $line = fgets($this->file);
        if (!$line) {
            if ($this->block == BlockTypes::preformatted) {
                $this->output .= "</pre>";
                $this->block = BlockTypes::text;
            }
            elseif ($this->block == BlockTypes::list) {
                $this->output .= "</ul>";
                $this->block = BlockTypes::text;
            }
            return;
        }

        if ($this->block == BlockTypes::preformatted) {
            $this->output .= $this->parse_pre($line);
        }
        elseif ($this->block == BlockTypes::list) {
            $this->output .= $this->parse_list($line);
        }
        else {
            $this->output .= $this->parse_text($line);
        }
    }

    private function parse_pre($line) {
        if ($line[0] == '`' && $line[1] == '`' && $line[2] == '`') {
            $this->block = BlockTypes::text;
            return "</pre>";
        } else {
            return $line;
        }
    }

    private function parse_list($line) {
        if ($line[0] != '*') {
            $this->block = BlockTypes::text;
            $html_line = $this->parse_text($line);
            return "</ul>$html_line";
        }
        else {
            return $this->parse_list_item($line);
        }
    }

    private function parse_text($line) {
        if ($line[0] == '`' && $line[1] == '`' && $line[2] == '`') {
            $this->block = BlockTypes::preformatted;
            $parsed_line = "<pre>";
        }
        elseif ($line[0] == '=' && $line[1] == '>') {
            $parsed_line = $this->parse_link($line);
        }
        elseif ($line[0] == '#') {
            $parsed_line = $this->parse_header($line);
        }
        elseif ($line[0] == '*') {
            $parsed_line = $this->parse_list_item($line);
        }
        elseif ($line[0] == '>') {
            $parsed_line = $this->parse_line_quote($line);
        } else {
            $parsed_line = $line."<br>\n";
        }
        return $parsed_line;
    }

    private function parse_link($line) {
        $link = trim(substr($line, 2));
        $link_parts = explode(" ", $link, 2);
        if (sizeof($link_parts) == 1) {
            $html_line = "➞ <a href=\"$link_parts[0]\">$link_parts[0]</a><br>\n";
        }
        else {
            $html_line = "➞ <a href=\"$link_parts[0]\">$link_parts[1]</a><br>\n";
        }
        return $html_line;
    }

    private function parse_header($line) {
        if ($line[1] == '#' && $line[2] == '#') {
            $header_text = trim(substr($line, 3));
            return "<h3>$header_text</h3>";
        }
        elseif ($line[1] == '#') {
            $header_text = trim(substr($line, 2));
            return "<h2>$header_text</h2>";
        }
        else {
            $header_text = trim(substr($line, 1));
            if (!$this->title) $this->title = $header_text;
            return "<h1>$header_text</h1>";
        }
    }

    private function parse_list_item($line) {
        $html_line = "";
        if ($this->block != BlockTypes::list) {
            $html_line .= "<ul>";
            $this->block = BlockTypes::list;
        }
        $item_text = trim(substr($line, 1));
        $html_line .= "<li>$item_text</li>";
        return $html_line;
    }

    private function parse_line_quote($line) {
        $quote_text = trim(substr($line, 1));
        return "<q>$quote_text</q><br>";
    }
}

