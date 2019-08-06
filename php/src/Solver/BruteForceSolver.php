<?php

namespace Dolondro\Sudoku\Solver;

use Dolondro\Sudoku\SudokuGrid;

class BruteForceSolver
{
    public function solve(SudokuGrid $grid) : ?SudokuGrid
    {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid->hasNumber($row, $col)) {
                    continue;
                }

                for ($num = 1; $num <= 9; $num ++){
                    $isValid = $grid->isValid($row, $col, $num);
                    if ($grid->isValid($row, $col, $num)) {
                        $grid->set($row, $col, $num);
                        $newGrid = $this->solve($grid);
                        if (isset($newGrid)) {
                            return $newGrid;
                        }
                    }
                }

                $grid->set($row, $col, null);
                return null;
            }
        }

        return $grid;
    }
}