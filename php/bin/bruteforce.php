<?php
include(__DIR__ . "/../vendor/autoload.php");
$solver = new \Dolondro\Sudoku\Solver\BruteForceSolver();
$loader = new \Dolondro\Sudoku\Loader\FileLoader();
$grid = $loader->load(__DIR__ . "/../../examples/example1.txt");

$microtime = microtime(true);
$grid = $solver->solve($grid);
$result = [];
foreach ($grid->toArray() as $array) {
    $result["result"][] = implode("", $array);
}
$result["time"] = microtime(true) - $microtime;
echo json_encode($result, JSON_PRETTY_PRINT);