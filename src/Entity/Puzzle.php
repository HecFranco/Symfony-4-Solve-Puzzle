<?php
// src/Entity/Puzzle.php
namespace App\Entity;

class Puzzle {
    protected $name;
    protected $file;
    public function getFile() {
        return $this->file;
    }
    public function setFile($file) {
        $this->file = $file;
    }
}