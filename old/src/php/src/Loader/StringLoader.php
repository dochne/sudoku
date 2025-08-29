<?php
/**
 * Created by PhpStorm.
 * User: Doug
 * Date: 16/03/2019
 * Time: 21:23
 */

namespace Dolondro\Sudoku\Loader;

use Dolondro\Sudoku\SudokuGrid;

class StringLoader implements LoaderInterface
{
    public function load($props) : SudokuGrid
    {
        $chunks = explode(" ", trim(chunk_split($props, 9, " ")));
        
        // foreach
        $array = [];
        foreach ($chunks as $row) {
            $row = trim($row);
            $array[] = array_map(function($v) { return $v === "0" ? null : (int)$v;}, str_split($row));
        }

        // var_dump($array);
        // die();
        return new SudokuGrid($array);
        

        // $array = [];
        // foreach ($rows as $row) {
        //     $row = str_pad($row, 9, " ");
        //     $array[] = array_map(function($v) { return $v === " " ? null : (int)$v;}, str_split($row));
        // }

        // return new SudokuGrid($array);
    }
}