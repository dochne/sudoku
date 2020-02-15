<?php
include(__DIR__ . "/../vendor/autoload.php");

$solver = new \Dolondro\Sudoku\Solver\BruteForceSolver();
$loader = new \Dolondro\Sudoku\Loader\FileLoader();
$grid = $loader->load($argv[1]);
$start = microtime(true);
$grid = $solver->solve($grid);
echo json_encode([
    "time" => microtime(true) - $start,
    "output" => $grid->print()
]);


