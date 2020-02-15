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
        foreach ($this->cells as $i => $value) {
            echo $value;
            if (($i + 1) % 9 === 0) {
                echo "\n";
            }
        }
    }
}

$grid = new Grid($cells, $cellLinks, $links);

function solve(Grid $grid) {
    if ($grid->pos === 81) {
        return $grid;
    }

    if ($grid->cells[$grid->pos] !== null) {
        $newGrid = clone $grid;
        $newGrid->pos++;
        $response = solve($newGrid);
        if ($response) {
            return $response;
        }
    } else {
        //$clinks = $grid->cellLinks[$grid->pos];

//        if (
//            count($grid->links[$grid->cellLinks[$grid->pos][0]]) === 0 ||
//            count($grid->links[$grid->cellLinks[$grid->pos][1]]) === 0 ||
//            count($grid->links[$grid->cellLinks[$grid->pos][2]]) === 0
//        ) {
//            return null;
//        }

        $possibleNumbers = array_intersect(
            array_keys($grid->links[$grid->cellLinks[$grid->pos][0]]),
            array_keys($grid->links[$grid->cellLinks[$grid->pos][1]]),
            array_keys($grid->links[$grid->cellLinks[$grid->pos][2]])
        );

        foreach ($possibleNumbers as $number) {
            $newGrid = clone $grid;
            if ($number == 0) {
                echo "foo\n";
            }
            $newGrid->cells[$grid->pos] = $number;
            unset(
                $newGrid->links[$grid->cellLinks[$grid->pos][0]][$number],
                $newGrid->links[$grid->cellLinks[$grid->pos][1]][$number],
                $newGrid->links[$grid->cellLinks[$grid->pos][2]][$number]
            );
            $newGrid->pos++;
            $response = solve($newGrid);
            if ($response) {
                return $response;
            }
        }
    }

//    array_intersect(
//        array_keys($grid->cellLinks[$grid->pos][0]), array_keys($grid->cellLinks[$grid->pos][1]), array_keys($grid->cellLinks[$grid->pos][2])) as $num) {

//    if (!is_array($grid->links[$grid->cellLinks[$grid->pos][2]])) {
//        echo "foo\n";
//    }


    return null;
}

$grid = solve($grid);
$grid->output();

//echo microtime(true) - $start . "\n";
