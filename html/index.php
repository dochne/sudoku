<?php

include("../vendor/autoload.php");

echo "<pre>";

echo "<style>table td{ border: 1px solid #000;}</style>";

function displayGrid(\Dolondro\Sudoku\SudokuGrid $grid)
{
    $resultArray = $grid->toArray();


    echo "<table style='border: 1px solid #000'>";
    foreach ($resultArray as $row => $data) {
        echo "<tr>";
        foreach ($data as $col => $value) {
            echo "<td>" . $value;
        }
    }
    echo "</table>";
}

$loader = new \Dolondro\Sudoku\Loader\FileLoader();
$grid = $loader->load(__DIR__ . "/../examples/example1.txt");

displayGrid($grid);
/*$startArray = $grid->toArray();
print_r($startArray);
die();*/
$solver = new \Dolondro\Sudoku\Solver\BruteForceSolver();

$solvers = [
    "BruteForce" => new \Dolondro\Sudoku\Solver\BruteForceSolver(),
    "DancingLink" => new \Dolondro\Sudoku\Solver\DancingLinkSolver()
];

echo "<table>";
echo "<thead>";
echo "<tr>";
echo "<th>Name";
foreach ($solvers as $name => $solver) {
    echo "<th>{$name}</th>";
}
echo "<tbody>";
echo "<tr><th>Benchmark</th>";
$results = [];
foreach ($solvers as $name => $solver) {
    $start = microtime(true);
    for ($x=0; $x<10; $x++) {
        $results[$name] = $solver->solve($grid);
    }
    $time = microtime(true) - $start;
    echo "<td>" . $time;
}

echo "<tr><th>Result";
foreach ($solvers as $name => $solver) {
    echo "<td>";
    displayGrid($results[$name]);
}


?>