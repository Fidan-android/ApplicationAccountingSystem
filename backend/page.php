<?php

    class Page {
        
        public $title, $html;

        public function __construct($title, $html) {
            $this->title = $title;
            $this->html = $html;
        }
        
    }

?>