// use std::io;
use std::fs::File;
use std::env;
// use std::collections::HashMap;
use std::collections::HashSet;
use std::io::{BufRead, BufReader};

use std::sync::atomic::{AtomicU32, Ordering};

use math::round;

extern crate math;

#[macro_use]
extern crate lazy_static;

type Cells = [usize; 81];
type Links = [usize; 27];
type CellLinks = [CellLink; 81];
type CellLink = [usize; 3];

#[derive(Clone)]
struct Grid {
    cells: Cells,
    cell_links: CellLinks,
    links: Links,
    empty_cells: HashSet<usize>,
    complete: bool
}

type NumberToBinaryMap = [usize; 10];
type BinaryToNumberMap = [usize; 257]; //HashMap<usize, usize>;
type BinaryToTotalNumbers = [usize; 512];
type BinaryToRepresentedNumbers = Vec<Vec<usize>>;
// type BinaryToRepresentedNumbers = [Vec<usize>; 512];

const fn number_to_binary_map () -> NumberToBinaryMap {
    let mut number_to_binary_map: NumberToBinaryMap = [0; 10];
    let mut n: usize = 1;
    while n < 10 {
        let value = (1 << n - 1) as usize;
        number_to_binary_map[n] = value;
        n = n + 1;
    }
    number_to_binary_map
}


const fn binary_to_number_map () -> BinaryToNumberMap {
    let mut binary_to_number_map: BinaryToNumberMap = [0; 257];
    let mut n: usize = 1;
    while n < 10 {
        let value = (1 << n - 1) as usize;
        binary_to_number_map[value] = n;
        n = n + 1;
    }
    binary_to_number_map
}

const fn binary_to_total_numbers () -> BinaryToTotalNumbers {
    // This speedily lets us know how many times 1 appears in the bitfield for a given input
    let mut binary_to_total_numbers = [0; 512];
    let mut n = 0;

    while n < 512 {
        let mut count = 0;
        let mut v = n;
        while v > 0 {
            count = count + 1;
            v = v & (v - 1)
        }
        binary_to_total_numbers[n] = count;
        n = n + 1;
    }
    binary_to_total_numbers
}

fn binary_to_represented_numbers(number_to_binary_map: NumberToBinaryMap) -> BinaryToRepresentedNumbers {
    
    let mut number_map = Vec::new(); // [Vec; 512];
    for n in 0..512 {
        let mut numbers = Vec::new();
        for i in 1..10 {
            if n & number_to_binary_map[i] != 0 {
                numbers.push(number_to_binary_map[i]);
            }
        }
        number_map.push(numbers)
    }
    number_map
}

const NUMBER_TO_BINARY_MAP: NumberToBinaryMap = number_to_binary_map();
const BINARY_TO_NUMBER_MAP: BinaryToNumberMap = binary_to_number_map();
const BINARY_TO_TOTAL_NUMBERS: BinaryToTotalNumbers = binary_to_total_numbers();

lazy_static! {
    static ref BINARY_TO_REPRESENTED_NUMBERS: BinaryToRepresentedNumbers = binary_to_represented_numbers(NUMBER_TO_BINARY_MAP);
}

static ATOMIC: AtomicU32 = AtomicU32::new(0);
static mut ATTEMPT_ATOMIC: i32 = 0;

fn main() {
    let args: Vec<String> = env::args().collect();
    let filename = &args[1];
    let cells = read_file(filename);
    let grid = build_grid(cells);
    
    let grid = solve(grid);
    if grid.complete {
        print_grid(grid);
    } else {
        println!("Unable to complete")
    }
}

fn solve(mut grid: Grid) -> Grid {
    if grid.empty_cells.len() == 0 {
        print_grid(grid);
        std::process::exit(0);
        //return grid;
    }

    let mut lowest_link_total = 10;
    let mut pos: usize = 0;
    let mut pos_key: usize = 0;

    // For each of the empty cells, look at how many valid numbers are left
    for id in grid.empty_cells.iter() {
        let l1 = grid.cell_links[*id][0];
        let l2 = grid.cell_links[*id][1];
        let l3 = grid.cell_links[*id][2];

        let key = grid.links[l1] & grid.links[l2] & grid.links[l3];
        let count_intersect = BINARY_TO_TOTAL_NUMBERS[key];

        // We want to find the entry with the smallest number of options
        if count_intersect < lowest_link_total {
            pos = *id;
            pos_key = key;

            // If it only has the one option, then we're going to want to apply this immediately and continue!
            if count_intersect == 1 {
                break;
            }

            if count_intersect == 0 {
                return grid;
            }

            lowest_link_total = count_intersect;
        }
    }

    let len = BINARY_TO_REPRESENTED_NUMBERS[pos_key].len(); 

    if (unsafe {ATTEMPT_ATOMIC} % 500 == 0) && len > 1 && thread_claim(len - 1) {
        grid = threaded_solve(grid, pos, pos_key);
        // println!("Returning from threaded solve");
        thread_release(len - 1);
        return grid;
    }
    
    unsafe {
        ATTEMPT_ATOMIC = ATTEMPT_ATOMIC + 1;
    }

    let l1 = grid.cell_links[pos][0];
    let l2 = grid.cell_links[pos][1];
    let l3 = grid.cell_links[pos][2];
    grid.empty_cells.remove(&pos);

    for n in 0..len {
        let number = BINARY_TO_REPRESENTED_NUMBERS[pos_key][n];
        grid.cells[pos] = number;

        grid.links[l1] = grid.links[l1] ^ number;
        grid.links[l2] = grid.links[l2] ^ number;
        grid.links[l3] = grid.links[l3] ^ number;

        grid = solve(grid);

        grid.links[l1] = grid.links[l1] | number;
        grid.links[l2] = grid.links[l2] | number;
        grid.links[l3] = grid.links[l3] | number;
    }

    grid.empty_cells.insert(pos);
    return grid
}

fn threaded_solve(mut grid: Grid, pos: usize, pos_key: usize) -> Grid {
    let l1 = grid.cell_links[pos][0];
    let l2 = grid.cell_links[pos][1];
    let l3 = grid.cell_links[pos][2];
    grid.empty_cells.remove(&pos);

    let mut handles = vec![];
    for n in 0..BINARY_TO_REPRESENTED_NUMBERS[pos_key].len() {
        let number = BINARY_TO_REPRESENTED_NUMBERS[pos_key][n];

        let mut new_grid = grid.clone();
        new_grid.cells[pos] = number;
        new_grid.links[l1] = new_grid.links[l1] ^ number;
        new_grid.links[l2] = new_grid.links[l2] ^ number;
        new_grid.links[l3] = new_grid.links[l3] ^ number;

        // println!("New thread!");
        let handle = std::thread::spawn(move || {
            solve(new_grid)
        });
        handles.push(handle)
    }
    
    // println!("\n{} handles", handles.len());
    for h in handles {
        //println!("Unwrapping handles");
        h.join().unwrap();
    }
    grid.empty_cells.insert(pos);

    grid
}

fn thread_claim(len: usize) -> bool {
    const MAX_THREADS: usize = 64;
    loop {
        
        let value = ATOMIC.load(Ordering::Relaxed);
        // println!("{} threads running", value);
        if value as usize + len > MAX_THREADS {
            return false;
        }

        if ATOMIC.compare_exchange_weak(value, value + len as u32, Ordering::SeqCst, Ordering::Relaxed).is_ok() {
            // println!("Claimed {} threads", len);
            return true;
        }
    }
}

fn thread_release(len: usize) -> bool {
    
    loop {
        let value = ATOMIC.load(Ordering::Relaxed);

        if ATOMIC.compare_exchange_weak(value, value - len as u32, Ordering::SeqCst, Ordering::Relaxed).is_ok() {
            //println!("Released {} threads", len);
            return true;
        }
    }
}

fn print_grid(grid: Grid) {
    for (key, value) in grid.cells.iter().enumerate() {
        if key % 9 == 0 {
            println!()
        }

        print!("{}", BINARY_TO_NUMBER_MAP[*value]);
    }
}

fn build_grid(cells: Cells) -> Grid {
    let mut links: Links = [511; 27];
    let mut cell_links: CellLinks = [[0; 3]; 81];

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

    // Cooool! We now have all the data we should need to solve this quickly!
    // We'll just quickly create ourselves a emptyCells HashSet ;)
    let mut empty_cells: HashSet<usize> = HashSet::new();
    //let mut empty_cells = 0;
    for n in 0..81 {
        if cells[n] == 0 {
            empty_cells.insert(n);
        }
    }

    Grid{
        cells,
        cell_links,
        links,
        empty_cells,
        complete: false
    }
}

fn read_file(filename: &String) -> Cells {
    let file = File::open(filename).unwrap();
    let reader = BufReader::new(file);

    // Setup our key structures!
    let mut cells: Cells = [0; 81];
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

    cells

}

#[cfg(test)]
mod tests {
    use binary_to_number_map;
    use number_to_binary_map;
    use binary_to_represented_numbers;

    #[test]
    fn it_works() {
        let binary_to_num_map = binary_to_number_map();
        let number_to_binary_map = number_to_binary_map();
        let rep_numbers = binary_to_represented_numbers(number_to_binary_map);

        assert_eq!(binary_to_num_map[1], 1);
        assert_eq!(binary_to_num_map[2], 2);
        assert_eq!(binary_to_num_map[4], 3);
        assert_eq!(binary_to_num_map[8], 4);
        assert_eq!(rep_numbers[1], vec![1]);
    }
}
