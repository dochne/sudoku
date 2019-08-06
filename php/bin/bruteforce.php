<?php
include(__DIR__ . "/../vendor/autoload.php");

$solver = new \Dolondro\Sudoku\Solver\BruteForceSolver();
$loader = new \Dolondro\Sudoku\Loader\FileLoader();
$grid = $loader->load($argv[1]);
$microtime = microtime(true);
$grid = $solver->solve($grid);
$grid->print();