<?php

use Dolondro\Sudoku\SudokuGrid;

include(__DIR__ . "/../vendor/autoload.php");

function toBinary(int $n)
{
    return 1 << $n - 1;
}

function fromBinary(int $n)
{
    return strlen(decbin($n));
//    print_r([$n, 1 >> $n]);
//    return 1 >> $n - 1;
}


// This largely deals with optimisations to the SudokuGrid, so we'll kind of ignore everything and just shove it inline
// for the time being

// we're expecting props to be a filename
$contents = file_get_contents($argv[1]);
$contents = str_replace("\r", "", $contents);
$rows = explode("\n", $contents);
$cells = [];

// Step 1, load it into a single non-assoc array
$i = 0;
foreach ($rows as $row) {
    for ($x=0; $x<9; ++$x) {
        $value = $row[$x] ?? " ";
        $cells[] = $value !== " " ? toBinary($value) : null;
    }
}

// Step 2, build a set of links for rows, cols and "blocks"
//$baseArray = [];
//for ($x=1; $x<=9; ++$x) {
//    $baseArray[$x] = true;
//}

const ROW_LINK_OFFSET = 0;
const COL_LINK_OFFSET = 9;
const BLOCK_LINK_OFFSET = 18;

$links = array_pad([], 27, 511);
$cellLinks = [];
$linksToCells = [];
foreach ($cells as $id => $cell) {
    // RowId
    $rowId = (int)floor($id / 9);
    $colId = $id % 9;

    //$rowLinkId =

    // Working out blockLinkId is a bit hairy:
    /**
     * -------------
     * | 0 | 1 | 2 |
     * -------------
     * | 3 | 4 | 5 |
     * -------------
     * | 6 | 7 | 8 |
     * -------------
     */
    $n1 = floor($rowId / 3);
    $n2 = floor($colId / 3);
    $blockId = (int)((3 * $n1) + $n2);

    $cellLinks[$id] = [
        ROW_LINK_OFFSET + $rowId,
        COL_LINK_OFFSET + $colId,
        BLOCK_LINK_OFFSET + $blockId
    ];

    $linksToCells[ROW_LINK_OFFSET + $rowId][] = $id;
}


// We now have:
//  - a grid with numbers
//  - a set of "links" which are really just number pools
//  - and a set of cell -> link mappings

// Next step, updating the cell link mappings to work with the original grid
foreach ($cells as $id => $value) {
    if ($value !== null) {
        // Uch! :D
        $links[$cellLinks[$id][0]] = $links[$cellLinks[$id][0]] ^ $value;
        $links[$cellLinks[$id][1]] = $links[$cellLinks[$id][1]] ^ $value;
        $links[$cellLinks[$id][2]] = $links[$cellLinks[$id][2]] ^ $value;
    }
}

class Grid {
    public $cells;
    public $cellLinks;
    public $links;
//    public $pos = 0;
//    public $endPos = 81;
    public $emptyCells = [];
    public $iterations = 0;

    public function __construct(array $cells, array $cellLinks, array $links)
    {
        $this->cells = $cells;
        $this->cellLinks = $cellLinks;
        $this->links = $links;
        foreach ($this->cells as $id => $value) {
            if ($value === null) {
                $this->emptyCells[$id] = true;
            }
        }
    }

    public function output()
    {
        $str = "";
        foreach ($this->cells as $i => $value) {
            $str .= $value;
            if (($i + 1) % 9 === 0) {
                $str .= "\n";
            }
        }
        return $str;
    }
}


global $map;
$map = [];
for ($x=1; $x<=9; $x++) {
    $map[$x] = toBinary($x);
//    $b = toBinary($x);
//    if ($b & $grid->links[$l1] & $grid->links[$l2] & $grid->links[$l3]) {
//        $intersect[] = $b;
//    }
}

global $totalMap;
global $possibleNumberMap;
$totalMap = [];

for ($x=0; $x<=511; $x++){
    $totalMap[$x] = array_sum(str_split(decbin($x)));
}


$possibleNumberMap = [];
for ($x=0; $x<511; $x++) {
    $possibleNumbers = [];
    for ($v=1; $v<=9; $v++) {
        if ($x & $map[$v]) {
            $possibleNumbers[] = $map[$v];
        }
    }
    $possibleNumberMap[$x] = $possibleNumbers;
}


/**
 * @param Grid $grid
 * @param array $cacheArray
 * @return |null
 */
function solve($grid) {
//    global $map;
    global $totalMap;
    global $possibleNumberMap;

    ++$grid->iterations;

    if (!$grid->emptyCells) {
        return $grid;
    }

    $lowestLinkTotal = 10;

    foreach ($grid->emptyCells as $id => $cell) {
        $l1 = $grid->cellLinks[$id][0];
        $l2 = $grid->cellLinks[$id][1];
        $l3 = $grid->cellLinks[$id][2];

        $key = $grid->links[$l1] & $grid->links[$l2] & $grid->links[$l3];
        $countIntersect = $totalMap[$key];

        if ($countIntersect < $lowestLinkTotal) {
            $pos = $id;
            $posKey = $key;

            if ($countIntersect === 1) {
                break;
            }

            if ($countIntersect === 0) {
                return null;
            }

            $lowestLinkTotal = $countIntersect;
        }
    }

    // Slightly more efficient
//    if ($countIntersect === 1 && count($grid->emptyCells) === 1) {
//        $grid->cells[$pos] = $possibleNumberMap[$posKey][0];
//        return $grid;
//    }

    $l1 = $grid->cellLinks[$pos][0];
    $l2 = $grid->cellLinks[$pos][1];
    $l3 = $grid->cellLinks[$pos][2];

    unset($grid->emptyCells[$pos]);

    foreach ($possibleNumberMap[$posKey] as $number) {
        $grid->cells[$pos] = $number;

        $grid->links[$l1] = $grid->links[$l1] ^ $number;
        $grid->links[$l2] = $grid->links[$l2] ^ $number;
        $grid->links[$l3] = $grid->links[$l3] ^ $number;

        $response = solve($grid);
        if ($response) {
            return $response;
        }

        $grid->links[$l1] = $grid->links[$l1] | $number;
        $grid->links[$l2] = $grid->links[$l2] | $number;
        $grid->links[$l3] = $grid->links[$l3] | $number;
    }
    $grid->cells[$pos] = null;
    $grid->emptyCells[$pos] = true;

    return null;
}

$grid = new Grid($cells, $cellLinks, $links);
$start = microtime(true);

$lowestLinkTotal = 10;
foreach ($grid->emptyCells as $id => $cell) {
    $l1 = $grid->cellLinks[$id][0];
    $l2 = $grid->cellLinks[$id][1];
    $l3 = $grid->cellLinks[$id][2];

    $key = $grid->links[$l1] & $grid->links[$l2] & $grid->links[$l3];
    $countIntersect = $totalMap[$key];

    if ($countIntersect < $lowestLinkTotal) {
        $pos = $id;
        $posKey = $key;

        if ($countIntersect === 1) {
            break;
        }

        if ($countIntersect === 0) {
            return null;
        }

        $lowestLinkTotal = $countIntersect;
    }
}

$possibleNumbers = $possibleNumberMap[$posKey];


$monitor = shmop_open(ftok(__FILE__, chr(0)), "c", 0644, 100);
shmop_delete($monitor);
$monitor = shmop_open(ftok(__FILE__, chr(0)), "c", 0644, 100);

$pids = [];
unset($grid->emptyCells[$pos]);
$n = 0;
//var_dump($possibleNumbers);
foreach ($possibleNumbers as $number) {
    $pid = pcntl_fork();
//    $pid = 0;
    if ($pid === -1) {
        die("fork failure");
    } elseif ($pid === 0) {
        $l1 = $grid->cellLinks[$pos][0];
        $l2 = $grid->cellLinks[$pos][1];
        $l3 = $grid->cellLinks[$pos][2];

        $grid->cells[$pos] = $number;
        $grid->links[$l1] = $grid->links[$l1] ^ $number;
        $grid->links[$l2] = $grid->links[$l2] ^ $number;
        $grid->links[$l3] = $grid->links[$l3] ^ $number;

        $response = solve($grid);
        if ($response !== null) {
            foreach ($grid->cells as $k => $value) {
                $grid->cells[$k] = fromBinary($value);
            }
            shmop_write($monitor, $grid->output(), 0);
        }
        exit(0);
    }
    $pids[$pid] = true;
}

while (count($pids) > 0 && ($changedPid = pcntl_waitpid(0, $status)) != -1) {
    unset($pids[$changedPid]);
    if (($content = trim(shmop_read($monitor, 0, 100))) !== "") {
        echo json_encode([
            "time" => microtime(true) - $start,
            "output" => $content,
            "iterations" => $grid->iterations
        ], JSON_PRETTY_PRINT);

        foreach ($pids as $pid => $_) {
            posix_kill($pid, 9);
            //pcntl_s($pid, 9);
        }
        exit(0);
    }
//    var_dump($changedPid);
//    exit(0);
}

exit("Failed");


solve($grid);




foreach ($grid->cells as $k => $value) {
    $grid->cells[$k] = fromBinary($value);
}

//echo "Time:" . microtime(true) - $start . "\n";

echo json_encode([
    "time" => microtime(true) - $start,
    "output" => $grid->output(),
    "iterations" => $grid->iterations
], JSON_PRETTY_PRINT);

