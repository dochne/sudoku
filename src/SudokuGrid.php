<?php

namespace Dolondro\Sudoku;

/**
 * This class is immutable!
 * Class SudokuGrid
 * @package Dolondro\Sudoku
 */
class SudokuGrid
{
    protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function hasNumber(int $row, int $col) : bool
    {
        return isset($this->data[$row][$col]);
    }

    public function isValid(int $row, int $col, int $value) : bool
    {
        // Rows first!
        for ($colI = 0; $colI < 9; $colI++) {
            if ($this->data[$row][$colI] === $value) {
                return false;
            }
        }

        // Then columns
        for ($rowI = 0; $rowI < 9; $rowI++) {
            if ($this->data[$rowI][$col] === $value) {
                return false;
            }
        }

        // Now, work out boxes
        $rowStart = floor($row % 3);
        $colStart = floor($col % 3);
        for ($rowI = $rowStart; $rowI < $rowStart + 3; $rowI++) {
            for ($colI = $colStart; $colI < $colStart + 3; $colI++) {
                if ($this->data[$rowI][$col] === $value) {
                    return false;
                }
            }
        }
        return true;
    }

    public function set(int $row, int $col, int $value) : SudokuGrid
    {
        $newGrid = clone $this;
        // Using the really horrible bit about PHP here...
        $newGrid->data[$row][$col] = $value;
        return $newGrid;
    }

    public function toArray()
    {
        return $this->data;
    }
}