<?php

namespace Dolondro\Sudoku\Solver;

use Dolondro\Sudoku\SudokuGrid;

class DancingLinkSolver
{
    public function solve(SudokuGrid $grid) : ?SudokuGrid
    {
        // In a linked list, we have nodes that have references to each other,
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid->hasNumber($row, $col)) {
                    continue;
                }

                for ($num = 1; $num <= 9; $num ++){
                    if ($grid->isValid($row, $col, $num)) {
                        $newGrid = $this->solve($grid->set($row, $col, $num));
                        if (isset($newGrid)) {
                            return $newGrid;
                        }
                    }
                }

                return null;
            }
        }

        return $grid;
    }
}