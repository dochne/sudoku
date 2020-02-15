<?php

namespace Dolondro\Sudoku\Andy;

/**
 * Class GridPrinter
 * @package Andywaite\Sudoku
 *
 * Simple CLI grid printer
 */
class GridPrinter
{
    /**
     * Dumb way to visualise grid on the CLI
     *
     * @param Grid $grid
     */
    public function printGrid($grid)
    {
        $str = "";
        if ($grid instanceof \Andywaite\Sudoku\Grid) {
            for ($y = 0; $y < 9; $y++) {
                for ($x = 0; $x < 9; $x++) {
                    $str .= $grid->getValue($x, $y);
                }
                $str .= "\n";
            }
            return $str;
        }

        for ($y = 0; $y < 9; $y++) {
            for ($x = 0; $x < 9; $x++) {
                $str .= $grid->grid[$x][$y];
            }
            $str .= "\n";
        }
        return $str;
    }
}
