<?php

include(__DIR__ . "/../vendor/autoload.php");

// Create our classes
$grid = new \Dolondro\Sudoku\Andy\Grid();
$printer = new \Dolondro\Sudoku\Andy\GridPrinter();
$solver = new \Dolondro\Sudoku\Andy\Solver(new \Dolondro\Sudoku\Andy\CellChecker());

// we're expecting props to be a filename
$contents = file_get_contents($argv[1]);
$contents = str_replace("\r", "", $contents);

$rows = explode("\n", $contents);
$array = [];

foreach ($rows as $row) {
    $row = str_pad($row, 9, " ");
    $array[] = array_map(function($v) { return $v === " " ? null : (int)$v;}, str_split($row));
}

// Populate grid with seed data
foreach ($array as $x => $array2) {
    foreach ($array2 as $y => $value) {
        if ($value !== null) {
            $grid->grid[$y][$x] = $value;
            //$grid->setValue($y, $x, $value);
        }
    }
}

// Echo unsolved state so we can see the "before"
//echo "\nUnsolved";
//$printer->printGrid($grid);

// Solve!
$start = microtime(true);
try {
    $solver->solve($grid);
    //echo "\n\nSolved";
} catch (Exception $e) {
    //echo "\n\nFailed to solve: ".$e->getMessage();
}
//
//// Calculate timings
//$end = microtime(true);
//
//echo $runTime . "\n";

// Show the completed grid

echo json_encode([
    "time" => microtime(true) - $start,
    "output" => $printer->printGrid($grid)
]);



// Show some stats
//echo "\nMoves: ".$grid->getMoves();
//echo "\nFailed paths: ".$grid->getUndos()."\n";
//echo "\nExecution: ".$runTime."\n";
