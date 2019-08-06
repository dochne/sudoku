//use std::io;
use std::fs::File;
use std::env;
use std::io::{BufRead, BufReader};
// use math::round::floor;
use math::round;


extern crate math;
//extern crate reqwest;

type Cells = [[u8; 9]; 9];

pub struct Grid {
    cells: Cells
}

impl Grid {
    fn set_value(&mut self, x: usize, y: usize, value: u8) {
        self.cells[x][y] = value;
    }

    fn is_valid(&mut self, row: usize, col: usize, value: u8) -> bool {
//        println!("Attempting {} in {},{}", value, row, col);


        for r_i in 0..9 {
            if self.cells[r_i][col] == value {
                return false;
            }
        }

        for c_i in 0..9 {
            if self.cells[row][c_i] == value {
                return false;
            }
        }

        let row_div = (row as i32) / 3;
        let col_div = (col as i32) / 3;
        let row_from_i = round::floor(row_div as f64, 0) as usize;
        let col_from_i = round::floor(col_div as f64, 0) as usize;
        let row_offset = row_from_i * 3;
        let col_offset = col_from_i * 3;

        for r_i in 0..3 {
            for c_i in 0..3 {

                if self.cells[row_offset + r_i][col_offset + c_i] == value {
                    return false;
                }
            }
        }

        true
    }

    fn print(&self) {
        for (_r_i, row) in self.cells.iter().enumerate() {
            for (_c_i, value) in row.iter().enumerate() {
                print!("{} ", value);
            }
            print!("\n");
        }
    }

    fn solve(&mut self) -> bool {
        for row_index in 0..9 {

            for col_index in 0..9 {
        /*for (row_index, row) in self.cells.iter().enumerate() {
            for (col_index, value) in row.iter().enumerate() {*/
                if self.cells[row_index][col_index] == 0 {
                    for x in 1..10 {
                        if self.is_valid(row_index, col_index, x) {
                            self.set_value(row_index, col_index, x);
                            if self.solve() {
                                return true
                            }
                        }
                    }
                    self.set_value(row_index, col_index, 0);
                    return false;
                }
            }
        }

        true
    }
}

fn main() {
    let args: Vec<String> = env::args().collect();
    let filename = &args[1];

    let file = File::open(filename).unwrap();
    let reader = BufReader::new(file);

    let sudoku_grid = &mut Grid { cells: [[0; 9]; 9] };

    for (row_index, line) in reader.lines().enumerate() {
        let line = line.unwrap(); // Ignore errors.

        for (col_index, value) in line.chars().enumerate() {
            if value != ' ' {
                let digit = value.to_digit(10);
                if digit.is_some() {
                    sudoku_grid.set_value(row_index, col_index, digit.unwrap() as u8);
                }
            }
        }
    }

    if sudoku_grid.solve() {
        sudoku_grid.print();
    } else {
        println!("Unsolvable!")
    }
}