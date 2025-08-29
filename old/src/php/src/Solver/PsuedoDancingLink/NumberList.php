<?php

namespace Dolondro\Sudoku\Solver\PsuedoDancingLink;

class NumberList
{
    protected $numbers = [];

    public function __construct()
    {
        //int $low, int $high
        //$this->numbers = array_flip(range($low, $high));
    }

    public function has(int $num)
    {
        return isset($this->numbers[$num]);
    }

    public function add(int $num)
    {
        $this->numbers[$num] = true;
    }

    public function remove(int $num)
    {
        unset($this->numbers[$num]);
    }
}