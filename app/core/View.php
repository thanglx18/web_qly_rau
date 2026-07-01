<?php

class View {
    public static function render($path) {
        require_once __DIR__ . "/../view/" . $path . ".php";
    }
}