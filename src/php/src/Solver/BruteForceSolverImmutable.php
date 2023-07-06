<?php

namespace Dolondro\Sudoku\Solver;

use Dolondro\Sudoku\SudokuGrid;

class BruteForceSolverImmutable
{
    public function solve(SudokuGrid $grid) : ?SudokuGrid
    {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid->hasNumber($row, $col)) {
                    continue;
                }

                for ($num = 1; $num <= 9; $num ++){
                    if ($grid->isValid($row, $col, $num)) {
                        $newGrid = $this->solve($grid->withSet($row, $col, $num));
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