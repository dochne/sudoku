<?php

use Dolondro\Sudoku\SudokuGrid;

include(__DIR__ . "/../vendor/autoload.php");



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
        $cells[] = $value !== " " ? (int)$value : null;
    }
}

// Step 2, build a set of links for rows, cols and "blocks"
$baseArray = [];
for ($x=1; $x<=9; ++$x) {
    $baseArray[$x] = true;
}

const ROW_LINK_OFFSET = 0;
const COL_LINK_OFFSET = 9;
const BLOCK_LINK_OFFSET = 18;

$links = array_pad([], 27, $baseArray);
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
        unset($links[$cellLinks[$id][0]][$value], $links[$cellLinks[$id][1]][$value], $links[$cellLinks[$id][2]][$value]);
    }
}


class Grid {
    public $cells;
    public $cellLinks;
    public $links;
    public $stack = [];
    public $grabStack = [];
//    public $pos = 0;
//    public $endPos = 81;
    public $emptyCells = [];
//    public $numberStack = [];
//    public $iStack = [];
//    public $lStack = [];
//    public $possibleNumberStack = [];
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
//
//$bestRun = 0;
//$bestRunId = 0;
//
//$currentRun = 0;
//$currentRunId = 0;
//
//foreach ($cells as $id => $value) {
//    if ($currentRun === 0) {
//        $currentRunId = $id;
//    }
//
//    if ($value === null) {
//        $currentRunId = $id;
//        $currentRun = 0;
//    } else {
//        $currentRun++;
//    }
//
//    if ($currentRun > $bestRun) {
//        $bestRun = $currentRun;
//        $bestRunId = $currentRunId;
//    }
//}


//print_r([$bestRun, $bestRunId]);
//exit(0);
$grid = new Grid($cells, $cellLinks, $links);
//$grid->pos = $bestRunId;
//$grid->endPos = $bestRunId + 81;

/**
 * @param Grid $grid
 * @param array $cacheArray
 * @return |null
 */
function solve($grid) {

    begin:
    //echo count($grid->emptyCells) . "\n";
    ++$grid->iterations;
    if (count($grid->emptyCells) === 0) {
        return $grid;
    }

    $lowestLinkTotal = 10;

    foreach ($grid->emptyCells as $id => $cell) {
//        $intersectValues = [];
//        foreach ($grid->cellLinks[$id] as $linkId) {
//            $intersectValues[] = array_keys($grid->links[$linkId]);
//        }
        $l1 = $grid->cellLinks[$id][0];
        $l2 = $grid->cellLinks[$id][1];
        $l3 = $grid->cellLinks[$id][2];

//         We could cache/invalidate this somewhere
        $intersect = array_intersect(
            array_keys($grid->links[$l1]),
            array_keys($grid->links[$l2]),
            array_keys($grid->links[$l3])
        );
//        $intersect = array_intersect(...$intersectValues);

        $countIntersect = count($intersect);

        if ($countIntersect < $lowestLinkTotal) {
            $pos = $id;
            $possibleNumbers = $intersect;
            if ($countIntersect <= 1) {
                break;
            }
            $lowestLinkTotal = $countIntersect;

        }
    }

    $possibleNumbers = array_values($possibleNumbers);

    if (count($possibleNumbers) === 0) {
        //return null;
        goto reset;
    }

//
    $l1 = $grid->cellLinks[$pos][0];
    $l2 = $grid->cellLinks[$pos][1];
    $l3 = $grid->cellLinks[$pos][2];

    // Todo: Work out why this unset can cause everything to be shit
    unset($grid->emptyCells[$pos]);

    //$grid->stack[] = $pos;

    $i = 0;
    iterate:
    if (isset($possibleNumbers[$i])) {
        $number = $possibleNumbers[$i];

        $grid->stack[] = [
            $number,
            $possibleNumbers,
            $i,
            [$l1, $l2, $l3],
            $pos
        ];

        $grid->cells[$pos] = $number;
        unset($grid->links[$l1][$number], $grid->links[$l2][$number], $grid->links[$l3][$number]);
        goto begin;
        reset:

        $popped = array_pop($grid->stack);
        $l1 = $popped[3][0];
        $l2 = $popped[3][1];
        $l3 = $popped[3][2];

        $grid->links[$l1][$popped[0]] = 1;
        $grid->links[$l2][$popped[0]] = 1;
        $grid->links[$l3][$popped[0]] = 1;
        $i = $popped[2];
        $possibleNumbers = $popped[1];
        $pos = $popped[4];
        ++$i;
        goto iterate;
    }

    //$pos = array_pop($grid->stack);
    $grid->cells[$pos] = null;
    $grid->emptyCells[$pos] = true;

    if ($grid->stack === 0) {
        return null;
    }
    //return null;
    //echo count($grid->stack) . "\n";
    $i = 0;
    goto reset;
}

$start = microtime(true);

solve($grid);
//echo "Time:" . microtime(true) - $start . "\n";

echo json_encode([
    "time" => microtime(true) - $start,
    "output" => $grid->output(),
    "iterations" => $grid->iterations
], JSON_PRETTY_PRINT);

