<?php
include(__DIR__ . "/../vendor/autoload.php");

$solver = new \Dolondro\Sudoku\Solver\BruteForceSolver();
$loader = new \Dolondro\Sudoku\Loader\StringLoader();

\Dolondro\Sudoku\NetworkSolver::handle("php:bruteforce", function($request) use ($loader, $solver) {
    $grid = $loader->load($request);
    $grid = $solver->solve($grid);
    return $grid->printLine();
});