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
for ($x=1; $x<=9; $x++) {
    $baseArray[$x] = true;
}

const ROW_LINK_OFFSET = 0;
const COL_LINK_OFFSET = 9;
const BLOCK_LINK_OFFSET = 18;

$links = array_pad([], 27, $baseArray);
$cellLinks = [];
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
    public $pos = 0;

    public function __construct(array $cells, array $cellLinks, array $links)
    {
        $this->cells = $cells;
        $this->cellLinks = $cellLinks;
        $this->links = $links;
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

$grid = new Grid($cells, $cellLinks, $links);

/**
 * @param Grid $grid
 * @return |null
 */
function solve($grid) {
    if ($grid->pos === 81) {
        return $grid;
    }

    if ($grid->cells[$grid->pos] !== null) {
        $grid->pos++;
        if ($response = solve($grid)) {
            return $grid;
        } else {
            $grid->pos--;
            return $response;
        }

    } else {
        $l1 = $grid->cellLinks[$grid->pos][0];
        $l2 = $grid->cellLinks[$grid->pos][1];
        $l3 = $grid->cellLinks[$grid->pos][2];

        $possibleNumbers = array_intersect(
            array_keys($grid->links[$l1]),
            array_keys($grid->links[$l2]),
            array_keys($grid->links[$l3])
        );


        foreach ($possibleNumbers as $number) {
            //$newGrid = clone $grid;
            $grid->cells[$grid->pos] = $number;
            unset($grid->links[$l1][$number], $grid->links[$l2][$number], $grid->links[$l3][$number]);
            $grid->pos++;


            $response = solve($grid);
            if ($response) {
                return $response;
            }

            $grid->pos--;
            $grid->cells[$grid->pos] = null;
            $grid->links[$l1][$number] = 1;
            $grid->links[$l2][$number] = 1;
            $grid->links[$l3][$number] = 1;
        }
    }

    return null;
}

$start = microtime(true);
solve($grid);
//echo "Time:" . microtime(true) - $start . "\n";

echo json_encode([
    "time" => microtime(true) - $start,
    "output" => $grid->output(),
    "iterations" => $grid->iterations
], JSON_PRETTY_PRINT);

