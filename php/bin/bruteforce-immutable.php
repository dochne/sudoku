<?php
include(__DIR__ . "/../vendor/autoload.php");
$solver = new \Dolondro\Sudoku\Solver\BruteForceSolverImmutable();
$loader = new \Dolondro\Sudoku\Loader\FileLoader();
$grid = $loader->load($argv[1]);

$microtime = microtime(true);
$grid = $solver->solve($grid);
$result = [];
foreach ($grid->toArray() as $array) {
    $result[] = implode(" ", $array);
}
echo implode("\n", $result);