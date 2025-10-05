<?php

class Grid
{
    protected $data = [];
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isValid(int $row, int $col, int $value) : bool
    {
        // Rows first!
        for ($colI = 0; $colI < 9; $colI++) {
            if ($this->data[$row][$colI] === $value) {
                return false;
            }
        }

        // Then columns
        for ($rowI = 0; $rowI < 9; $rowI++) {
            if ($this->data[$rowI][$col] === $value) {
                return false;
            }
        }

        // Now, work out boxes
        $rowStart = (int)floor($row / 3) * 3;
        $colStart = (int)floor($col / 3) * 3;
        for ($rowI = $rowStart; $rowI < $rowStart + 3; $rowI++) {
            for ($colI = $colStart; $colI < $colStart + 3; $colI++) {
                if ($this->data[$rowI][$colI] == $value) {
                    return false;
                }
            }
        }

        return true;
    }

    public function set(int $row, int $col, ?int $value)
    {
        $this->data[$row][$col] = $value;
    }

    public function solve() : bool
    {
        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                if (!isset($this->data[$row][$col])) {
                    var_dump($this->data);
                    die();
                }
                if ($this->data[$row][$col] !== 0) {
                    continue;
                }

                for ($num = 1; $num <= 9; $num ++){
                    if ($this->isValid($row, $col, $num)) {
                        $this->set($row, $col, $num);
                        if ($this->solve()) {
                            return true;
                        }
                    }
                }

                $this->set($row, $col, 0);
                return false;
            }
        }
        return true;
    }

    public function output()
    {
        return implode("", array_map(fn($v) => implode("", $v), $this->data));
    }
}

function solve(string $string) {
    $data = array_chunk(array_map(fn($v) => (int)$v, str_split($string)), 9);
    $grid = new Grid($data);
    $grid->solve();
    return $grid->output();
}

if ($_ENV["SUDOKU"]) {
    $content = trim($_ENV["SUDOKU"]);
    echo solve($content) . "\n";
    exit(0);
}

$sock = stream_socket_client('unix:////tmp/sudoku.sock', $errno, $errstr);

fwrite($sock, 'php:basic');
while ($sock) {
    $content = fread($sock, 81);
    if ($content === "") {
        break;
    }

    fwrite($sock, solve($content));
}
fclose($sock);
