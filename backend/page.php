<?php

    class Page {
        
        private $title, $html;

        public function __construct($title, $html) {
            $this->title = $title;
            $this->html = $html;
        }
        
    }

?>