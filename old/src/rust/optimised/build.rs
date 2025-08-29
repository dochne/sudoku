use std::collections::HashMap;

fn main() {
    println!("cargo:rerun-if-changed=build.rs");

    let out_dir = std::env::var_os("OUT_DIR").unwrap();
    let path = std::path::Path::new(&out_dir).join("constants.rs");

    let mut s = String::new();
    // We only care about 1,9 but 0 indexing makes this easier
    s.push_str(
        &vec_to_string(
            &"BIN_MAP",
            &build_bin_map()
        )
    );

    s.push_str(
        &vec_to_string(
            "TOTAL_MAP",
            &build_total_map()
        )
    );


    s.push_str(
        &vec_to_string(
            "INVERSE_MAP",
            &build_inverse_map()
        )
    );



    


    println!("{}", s);
    // bin_map = initialize_bin_map();
    std::fs::write(&path, s).unwrap();
    

    // std::fs::write(&path, "pub const FOO: &str = \"bar\";").unwrap();
}

// This maps a given number into the binary representation we'll use for that number
// 1 => 00000001, 2 => 00000010, 3 => 00000100 etc
fn build_bin_map() -> Vec<String> {
    (0..10)
        .into_iter()
        .map(|n|
            match n {
                0 => 0,
                _ => 1 << n - 1
            }.to_string()
        )
        .collect()
}

// Takes a number that has all of the numbers &'d into it, and convert it back into 
fn build_total_map() -> Vec<String> {
    (0..512)
        .into_iter()
        .map(|n| {
            let mut count = 0;
            let mut v = n;
            while v > 0 {
                count += 1;
                v = v & (v - 1)
            }
            count.to_string()
        })
        .collect()
}

fn build_inverse_map() -> Vec<String>{
    // (0..10)
    //     .into_iter()
    //     .map(|n|
    //         match n {
    //             0 => 0,
    //             _ => 1 << n - 1
    //         }.to_string()
    //     )
    //     .collect()
        
    let mut temp_map: HashMap<usize, usize> = HashMap::new();
    for n in 1..10 {
        let value = (1 << n - 1) as usize;
        temp_map.insert(value, n);
    }

    // let mut inverse_map = [0; 256];
    (0..257)
        .into_iter()
        .map(|n| if temp_map.contains_key(&n) { temp_map.get(&n).unwrap().to_string() } else { "0".to_string() })
        .collect()
    // inverse_map
}


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



fn vec_to_string(name: &str, vec: &Vec<String>) -> String{
    let mut s = String::new();
    s.push_str(&format!("pub const {}: [i32; {}] = [", name, vec.len()));
    s.push_str(&vec.join(","));
    s.push_str("];\n");
    s
    // let mut array = vec![];
    // array.push(String::from("0"));
    // for n in 1..10 {
    //     array.push((1 << n - 1).to_string());
    // }
    // s.push_str(&array.join(","));
    // s.push_str("];");
    // s
}


// fn initialize_bin_map() -> Vec<usize> {
//     // let mut inverse_map: HashMap<usize, usize> = HashMap::new();
    
// }