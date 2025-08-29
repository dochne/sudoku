
fn initialize_bin_map() -> Vec<usize> {
    // let mut inverse_map: HashMap<usize, usize> = HashMap::new();
    let mut bin_map = [0; 10];
    for n in 1..10 {
        let value = (1 << n - 1) as usize;
        bin_map[n] = value;
        // inverse_map.insert(value, n);
    }
    bin_map
}

// fn initialize_inverse_map() -> HashMap<usize, usize>{
//     let mut inverse_map: HashMap<usize, usize> = HashMap::new();
//     // let mut bin_map = [0; 10];
//     for n in 1..10 {
//         let value = (1 << n - 1) as usize;
//         // bin_map[n] = value;
//         inverse_map.insert(value, n);
//     }
//     inverse_map
// }

// let mut inverse_map: HashMap<usize, usize> = HashMap::new();
// let mut bin_map = [0; 10];
// for n in 1..10 {
//     let value = (1 << n - 1) as usize;
//     bin_map[n] = value;
//     inverse_map.insert(value, n);
// }

// This speedily lets us know how many times 1 appears in the bitfield for a given input


// let bin_map = bin_map;
// let inverse_map = inverse_map;
// let total_map = total_map;


fn initialize_number_map(){
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
    number_map
}

fn initialize_total_map() -> Vec<Vec<usize>> {
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
    total_map
}

// fn initialize_total_map() -> [usize; 512] {

// }
