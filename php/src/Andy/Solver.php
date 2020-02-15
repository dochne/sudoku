<?php

namespace Dolondro\Sudoku\Andy;

/**
 * Class Solver
 * @package Andywaite\Sudoku
 *
 * Solves a Sudoku puzzle!
 */
class Solver
{
    /**
     * @var CellChecker
     */
    private $cellChecker;

    public function __construct(CellChecker $cellChecker)
    {
        $this->cellChecker = $cellChecker;
    }

    /**
     * Look for a place where only one value will work
     *
     * @param $grid
     * @return array|null
     */
    protected function getObviousMove(Grid $grid): ?array
    {
        // Loop cols
        for ($x = 0; $x < 9; $x++) {
            // Loop rows
            for ($y = 0; $y < 9; $y++) {
                if ($grid->grid[$x][$y] === null) {

                    $moves = $this->cellChecker->getValidMoves($grid, $x, $y);

                    // Only one valid move - let's make it!
                    if (count($moves) === 1) {
                        return [
                            'x' => $x,
                            'y' => $y,
                            'value' => $moves[0]
                        ];
                    }
                }
            }
        }

        return null;
    }


    /**
     * Attempt to solve a Sudoku puzzle
     *
     * @param Grid $grid
     * @return bool
     * @throws \Exception
     */
    public function solve(Grid $grid): bool
    {
        // Before we brute force, try and see if there's a place where there's only one option. This reduces execution on a hard puzzle from ~6s to ~0.8s but possibly makes easy puzzles take slightly longer?
        if ($move = $this->getObviousMove($grid)) {

            // If there is a place where only one option works, make it
            //$grid->setValue($move['x'], $move['y'], $move['value']);
            $grid->grid[$move['x']][$move['y']] = $move['value'];

            // Recursively solve
            if ($this->solve($grid)) {
                return true;
            }

            // Even though this HAS to be right, a previous brute force move may have been wrong, so we may need to backtrack
            // Undo and pass up the chain we were wrong :(
            $grid->grid[$move['x']][$move['y']] = null;
            return false;
        }

        // Now brute force

        // Loop cols
        for ($x = 0; $x < 9; $x++) {

            // Loop rows
            for ($y = 0; $y < 9; $y++) {

                // Check if this is empty cell
                //if (!$grid->isEmpty($x, $y)) {
                if ($grid->grid[$x][$y] !== null) {
                    // Next cell
                    continue;
                }

                // Loop through possible values
                for ($try = 1; $try <= 9; $try++) {

                    // If invalid move (i.e. collision with other cells)
                    if (!$this->cellChecker->isValidMove($grid, $x, $y, $try)) {
                        // Next possible value
                        continue;
                    }

                    // Set value
                    //$grid->setValue($x, $y, $try);
                    $grid->grid[$x][$y] = $try;

                    // Recursively solve
                    if ($this->solve($grid)) {
                        // Yay, solved!
                        return true;
                    }

                    // Must have failed, backtrack for this cell and try next
                    //$grid->nullValue($x, $y);
                    $grid->grid[$x][$y] = null;

                }

                // This didn't work, try another route
                return false;
            }
        }

        // All complete
        return true;
    }
}
