//use std::io;
use std::fs::File;
use std::env;
use std::collections::HashMap;
use std::collections::HashSet;
use std::io::{BufRead, BufReader};
// use math::round::floor;
use math::round;


extern crate math;
//extern crate reqwest;


/*
links: ,
cell_links,
empty_cells,
number_map,
total_map
*/


type Cells = [usize; 81];
type Links = [usize; 27];
type CellLinks = [[usize; 3]; 81];

pub struct Grid {
    cells: Cells,
    cell_links: CellLinks,
    links: Links,
    empty_cells: HashSet<usize>,
    number_map: Vec<Vec<usize>>,
    total_map: [usize; 512],
    inverse_map: HashMap<usize, usize>
}

impl Grid {

    fn print(&self) {
        for (key, value) in self.cells.iter().enumerate() {
            if key % 9 == 0 {
                println!()
            }

            print!("{}", self.inverse_map[value]);
        }
    }

    fn solve(&mut self) -> bool {
        if self.empty_cells.len() == 0 {
            return true;
        }

        let mut lowest_link_total = 10;
        let mut pos: usize = 0;
        let mut pos_key: usize = 0;

        for id in self.empty_cells.iter() {
            let l1 = self.cell_links[*id][0];
            let l2 = self.cell_links[*id][1];
            let l3 = self.cell_links[*id][2];

            let key = self.links[l1] & self.links[l2] & self.links[l3];
            let count_intersect = self.total_map[key];

            if count_intersect < lowest_link_total {
                pos = *id;
                pos_key = key;

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


        //let numbers = self.number_map[pos_key].iter().enumerate();
        for n in 0..self.number_map[pos_key].len() {
            let number = self.number_map[pos_key][n];

            self.cells[pos] = number;

            self.links[l1] = self.links[l1] ^ number;
            self.links[l2] = self.links[l2] ^ number;
            self.links[l3] = self.links[l3] ^ number;

            if self.solve() {
                return true;
            }

            self.links[l1] = self.links[l1] | number;
            self.links[l2] = self.links[l2] | number;
            self.links[l3] = self.links[l3] | number;
        }

        self.empty_cells.insert(pos);

        false

    }
}

fn main() {
    let args: Vec<String> = env::args().collect();
    let filename = &args[1];

    let file = File::open(filename).unwrap();
    let reader = BufReader::new(file);

    // Setup our key structures!
    let mut cells: Cells = [0; 81];
    let mut links: Links = [511; 27];
    let mut cell_links: CellLinks = [[0; 3]; 81];

    // Populate these key structures!

    // Start by populating the cells themselves!
    for (row_index, line) in reader.lines().enumerate() {
        let line = line.unwrap(); // Ignore errors.

        for (col_index, value) in line.chars().enumerate() {
            if value != ' ' {
                let digit = value.to_digit(10);
                if digit.is_some() {
                    cells[(row_index * 9) + col_index] = 1 << (digit.unwrap() as u8) - 1;
                }
            }
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
            links[cell_links[key][0]] = links[cell_links[key][0]] ^ cells[key];
            links[cell_links[key][1]] = links[cell_links[key][1]] ^ cells[key];
            links[cell_links[key][2]] = links[cell_links[key][2]] ^ cells[key];
        }
    }


    // Cool! All that remains is creating the maps we'll be using to be super duper fast!
    // A speedy map of our number (say, 3) to it's binary representation (8)
    let mut bin_map = [0; 10];
    let mut inverse_map: HashMap<usize, usize> = HashMap::new();
    for n in 1..10 {
        let value = (1 << n - 1) as usize;
        bin_map[n] = value;
        inverse_map.insert(value, n);
    }
    let bin_map = bin_map;
    let inverse_map = inverse_map;

    // This speedily lets us know how many times 1 appears in the bitfield for a given input
    let mut total_map = [0; 512];
    for n in 0..512 {
        let mut count = 0;
        let mut v = n;
        while v > 0 {
            count = count + 1;
            v = v & (v - 1)
        }
        total_map[n] = count;
    }
    let total_map = total_map;

    let mut number_map = Vec::new(); // [Vec; 512];
    for n in 0..512 {
        let mut numbers = Vec::new();
        for i in 1..10 {
            if n & bin_map[i] != 0 {
                numbers.push(bin_map[i]);
            }
        }
        number_map.push(numbers)
    }
    let number_map = number_map;


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
        empty_cells,
        number_map,
        total_map,
        inverse_map
    };

    if sudoku_grid.solve() {
        sudoku_grid.print();
    } else {
        println!("Unsolvable!")
    }
}