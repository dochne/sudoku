<?php
/**
 * Created by PhpStorm.
 * User: Doug
 * Date: 16/03/2019
 * Time: 21:23
 */

namespace Dolondro\Sudoku\Loader;

use Dolondro\Sudoku\SudokuGrid;

class FileLoader implements LoaderInterface
{
    public function load($props) : SudokuGrid
    {
        // we're expecting props to be a filename
        $contents = file_get_contents($props);
        $contents = str_replace("\r", "", $contents);

        $rows = explode("\n", $contents);
        $array = [];

        foreach ($rows as $row) {
            $row = str_pad($row, 9, " ");
            $array[] = array_map(function($v) { return $v === " " ? null : (int)$v;}, str_split($row));
        }

        return new SudokuGrid($array);
    }
}