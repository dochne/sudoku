<?php


use Dolondro\Sudoku\SudokuGrid;

function toBinary(int $n){
    return 1 << $n - 1;
}

function fromBinary(int $n) {
    return strlen(decbin($n));
}

// Generate the ahead of time constants
$TOTAL_MAP = [];
$POSSIBLE_NUMBER_MAP = [];
$BINARY_MAP = [];
$INVERTED_BINARY_MAP = [];

for ($x=1; $x<=9; $x++) {
    $BINARY_MAP[$x] = toBinary($x);
    $INVERTED_BINARY_MAP[toBinary($x)] = $x;
}

define("BINARY_MAP", $BINARY_MAP);
define("INVERTED_BINARY_MAP", $INVERTED_BINARY_MAP);

for ($x=0; $x<=511; $x++){
    $TOTAL_MAP[$x] = array_sum(str_split(decbin($x)));
}
define("TOTAL_MAP", $TOTAL_MAP);

for ($x=0; $x<511; $x++) {
    $possibleNumbers = [];
    for ($v=1; $v<=9; $v++) {
        if ($x & $BINARY_MAP[$v]) {
            $possibleNumbers[] = $BINARY_MAP[$v];
        }
    }
    $POSSIBLE_NUMBER_MAP[$x] = $possibleNumbers;
}
define("POSSIBLE_NUMBER_MAP", $POSSIBLE_NUMBER_MAP);


const ROW_LINK_OFFSET = 0;
const COL_LINK_OFFSET = 9;
const BLOCK_LINK_OFFSET = 18;

class Grid
{
    public array $cellLinks;
    public array $links;
    public $emptyCells = [];

    public function __construct(
        public array $cells,
    )
    {
        $this->links = array_pad([], 27, 511);
        foreach ($cells as $id => $cell) {
            $rowId = (int)floor($id / 9);
            $colId = $id % 9;
            $this->cellLinks[] = [
                ROW_LINK_OFFSET + $rowId,
                COL_LINK_OFFSET + $colId,
                BLOCK_LINK_OFFSET + (int)((3 * floor($rowId / 3)) + floor($colId / 3))
            ];
        }

        // Next step, updating the cell link mappings to work with the original grid
        foreach ($cells as $id => $value) {
            if ($value === null) {
                $this->emptyCells[$id] = true;
            } else {
                $this->links[$this->cellLinks[$id][0]] ^= $value;
                $this->links[$this->cellLinks[$id][1]] ^= $value;
                $this->links[$this->cellLinks[$id][2]] ^= $value;
            }
        }
    }

    public function solve()
    {
        if (count($this->emptyCells) === 0) {
            return true;
        }

        $lowestLinkTotal = 10;

        foreach ($this->emptyCells as $id => $cell) {
            $l1 = $this->cellLinks[$id][0];
            $l2 = $this->cellLinks[$id][1];
            $l3 = $this->cellLinks[$id][2];

            $key = $this->links[$l1] & $this->links[$l2] & $this->links[$l3];
            $countIntersect = TOTAL_MAP[$key];

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

        $l1 = $this->cellLinks[$pos][0];
        $l2 = $this->cellLinks[$pos][1];
        $l3 = $this->cellLinks[$pos][2];

        unset($this->emptyCells[$pos]);

        foreach (POSSIBLE_NUMBER_MAP[$posKey] as $number) {
            $this->cells[$pos] = $number;

            $this->links[$l1] ^= $number;
            $this->links[$l2] ^= $number;
            $this->links[$l3] ^= $number;

            $response = $this->solve();
            if ($response) {
                return $response;
            }

            $this->links[$l1] |= $number;
            $this->links[$l2] |= $number;
            $this->links[$l3] |= $number;
        }

        $this->cells[$pos] = null;
        $this->emptyCells[$pos] = true;

        return null;
    }

    public function output()
    {
        return implode("", array_map(fn($v) => INVERTED_BINARY_MAP[$v], $this->cells));
    }
}

function solve(string $string) {
    $cells = array_map(fn(string $value) => $value !== "0" ? toBinary($value) : null, str_split($string));
    $grid = new Grid($cells);
    $grid->solve();
    return $grid->output();
}

if ($_ENV["SUDOKU"]) {
    $content = trim($_ENV["SUDOKU"]);
    echo solve($content) . "\n";
    exit(0);
}

$sock = stream_socket_client('unix:////tmp/sudoku.sock', $errno, $errstr);

fwrite($sock, 'php:optimised');
while ($sock) {
    $content = fread($sock, 81);
    if ($content === "") {
        break;
    }

    fwrite($sock, solve($content));
}
fclose($sock);
