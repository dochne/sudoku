use once_cell::sync::Lazy;
use std::collections::HashMap;

pub const BINARY_MAP: [usize; 10] = [0, 1, 2, 4, 8, 16, 32, 64, 128, 256];

pub static INVERTED_BINARY_MAP: Lazy<HashMap<usize, String>> = Lazy::new(|| {
    let mut map = HashMap::new();
    for n in 1..10 {
        map.insert((1 << n - 1) as usize, n.to_string());
    }
    map
});

pub static TOTAL_MAP: Lazy<[u8; 512]> = Lazy::new(|| {
    let mut total_map = [0u8; 512];
    for n in 0..512 {
        let mut count = 0;
        let mut v = n;
        while v > 0 {
            count += 1;
            v &= v - 1;
        }
        total_map[n] = count;
    }
    total_map
});

pub static NUMBER_MAP: Lazy<Vec<Vec<usize>>> = Lazy::new(|| {
    let mut number_map = Vec::with_capacity(512);
    for n in 0..512 {
        let mut numbers = Vec::new();
        for i in 1..10 {
            if n & BINARY_MAP[i] != 0 {
                numbers.push(BINARY_MAP[i]);
            }
        }
        number_map.push(numbers);
    }
    number_map
});
