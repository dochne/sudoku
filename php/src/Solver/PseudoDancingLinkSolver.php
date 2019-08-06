<?php

namespace Dolondro\Sudoku\Solver;

use Dolondro\Sudoku\Solver\PsuedoDancingLink\NumberList;
use Dolondro\Sudoku\SudokuGrid;

class PseudoDancingLinkSolver
{
    public function solve(SudokuGrid $grid) : ?SudokuGrid
    {
        /**
         * @var NumberList[] $rows
         * @var NumberList[] $cols
         * @var NumberList[] $temp
         * @var NumberList[] $blocks
         */
        $rows = [];
        $cols = [];
        for($x=0; $x<9; $x++) {
            $rows[$x] = new NumberList();
            //$rows[$x] = new NumberList(1, 9);
            //$cols[$x] = new NumberList(1, 9);
            $cols[$x] = new NumberList();
        }

        $temp = [];
        for ($x=0; $x<3; $x++) {
            for ($y=0; $y<3; $y++) {
                $temp[$x.$y] = new NumberList();
            }
        }

        $blocks = [];
        for ($x=0; $x<9; $x++) {
            for ($y=0; $y<9; $y++) {
                $blocks[$x . $y] = $temp[floor($x/3) . floor($y / 3)];
            }
        }


        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid->hasNumber($row, $col)) {
                    $value = $grid->get($row, $col);
                    $rows[$row]->add($value);
                    $cols[$col]->add($value);
                    $blocks[$row . $col]->add($value);
                }
            }
        }

        return $this->recursiveSolve($grid, $rows, $cols, $blocks);
    }

    /**
     * @param SudokuGrid $grid
     * @param NumberList[] $rows
     * @param NumberList[] $cols
     * @param NumberList[] $blocks
     * @return SudokuGrid|null
     */
    protected function recursiveSolve(SudokuGrid $grid, array $rows, array $cols, array $blocks)
    {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if ($grid->hasNumber($row, $col)) {
                    continue;
                }

                for ($num = 1; $num <= 9; $num++){
                    if ($rows[$row]->has($num) || $cols[$col]->has($num) || $blocks[$row . $col]->has($num)) {
                        continue;
                    }

                    $rows[$row]->add($num);
                    $cols[$col]->add($num);
                    $blocks[$row . $col]->add($num);

                    $newGrid = $this->recursiveSolve($grid->withSet($row, $col, $num), $rows, $cols, $blocks);
                    if (isset($newGrid)) {
                        return $newGrid;
                    }

                    $rows[$row]->remove($num);
                    $cols[$col]->remove($num);
                    $blocks[$row . $col]->remove($num);
                }

                return null;
            }
        }

        return $grid;
    }
}