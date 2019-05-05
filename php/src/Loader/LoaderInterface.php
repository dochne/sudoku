<?php

namespace Dolondro\Sudoku\Loader;

use Dolondro\Sudoku\SudokuGrid;

interface LoaderInterface
{
    public function load($props) : SudokuGrid;
}