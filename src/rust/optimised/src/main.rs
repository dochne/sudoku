mod constants;
extern crate math;
extern crate once_cell;

use constants::{BINARY_MAP, INVERTED_BINARY_MAP, NUMBER_MAP, TOTAL_MAP};

use std::collections::HashSet;
use std::str;
use std::env;
use math::round;

type Cells = [usize; 81];
type Links = [usize; 27];
type CellLinks = [[usize; 3]; 81];

pub struct Grid {
    cells: Cells,
    cell_links: CellLinks,
    links: Links,
    empty_cells: HashSet<usize>
}

impl Grid {
    fn output(&self) -> String {
        self.cells.iter().map(|value| INVERTED_BINARY_MAP[value].as_str()).collect::<String>()
    }

    fn solve(&mut self) -> bool {
        if self.empty_cells.len() == 0 {
            return true;
        }

        let mut lowest_link_total = 10;
        let mut pos: usize = 0;
        let mut pos_key: usize = 0;

        // For each of the empty cells, look at how many valid numbers are left
        for id in self.empty_cells.iter() {
            let l1 = self.cell_links[*id][0];
            let l2 = self.cell_links[*id][1];
            let l3 = self.cell_links[*id][2];

            let key = self.links[l1] & self.links[l2] & self.links[l3];
            let count_intersect = TOTAL_MAP[key];

            // We want to find the entry with the smallest number of options
            if count_intersect < lowest_link_total {
                pos = *id;
                pos_key = key;

                // If it only has the one option, then we're going to want to apply this immediately and continue!
                if count_intersect == 1 {
                    break;
                }

                if count_intersect == 0 {
                    return false;
                }

                lowest_link_total = count_intersect;
            }
        }

        let l1 = self.cell_links[pos][0];
        let l2 = self.cell_links[pos][1];
        let l3 = self.cell_links[pos][2];
        self.empty_cells.remove(&pos);

        for n in 0..NUMBER_MAP[pos_key].len() {
            let number = NUMBER_MAP[pos_key][n];

            self.cells[pos] = number;

            self.links[l1] ^= number;
            self.links[l2] ^= number;
            self.links[l3] ^= number;

            if self.solve() {
                return true;
            }

            self.links[l1] |= number;
            self.links[l2] |= number;
            self.links[l3] |= number;
        }

        self.empty_cells.insert(pos);

        false

    }
}

fn solve(input: &str) -> String {
    // Setup our key structures!
    let mut cells: Cells = [0; 81];
    let mut links: Links = [511; 27];
    let mut cell_links: CellLinks = [[0; 3]; 81];

    // Start by populating the cells themselves!
    for (index, value) in input.char_indices() {
        let digit = value.to_digit(10).unwrap();
        if digit != 0 {
            cells[index] = BINARY_MAP[digit as usize]
        }
    }
    
    // Then continue by populating the cell_links!
    let row_link_offset = 0;
    let col_link_offset = 9;
    let block_link_offset = 18;

    for key in 0..81 {
        let row_id = round::floor((key / 9) as f64, 0) as usize;
        let col_id = (key % 9) as usize;
        let block_id = (round::floor((row_id / 3) as f64, 0) as usize * 3) + (round::floor((col_id / 3) as f64, 0)) as usize;
        cell_links[key] = [
            row_link_offset + row_id,
            col_link_offset + col_id,
            block_link_offset + block_id
        ];
    }

    // And we'll follow it up by populating the links! :)
    for key in 0..81 {
        if cells[key] != 0 {
            links[cell_links[key][0]] ^= cells[key];
            links[cell_links[key][1]] ^= cells[key];
            links[cell_links[key][2]] ^= cells[key];
        }
    }


    // Cooool! We now have all the data we should need to solve this quickly!
    // We'll just quickly create ourselves a emptyCells HashSet ;)
    let mut empty_cells: HashSet<usize> = HashSet::new();
    for n in 0..81 {
        if cells[n] == 0 {
            empty_cells.insert(n);
        }
    }

    // Now let's make us a grid!
    let sudoku_grid = &mut Grid {
        cells,
        links,
        cell_links,
        empty_cells
    };

    sudoku_grid.solve();
    sudoku_grid.output()
}

fn main() {
    let sudoku = env::var("SUDOKU").unwrap_or_default();
    if sudoku.is_empty() {
        socket_wrapper::run(String::from("rust:optimised"), |input| solve(&input))
    } else {
        println!("{}", solve(&sudoku))
    }
}