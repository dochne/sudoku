use std::os::unix::net::UnixStream;
// use std::time::{SystemTime, UNIX_EPOCH};
use std::io::{Read, Write};
// use math::round;
// use std::str;


const SOCKET_PATH: &str = "/tmp/sudoku.sock";

pub fn run(name: String, solve: fn(String) -> String) {
    let mut stream = UnixStream::connect(SOCKET_PATH).unwrap();
    let mut buffer = [0;81];
    stream.write_all(name.as_bytes()).unwrap();

    loop {
        stream.read_exact(&mut buffer).unwrap();
        let input = String::from_utf8_lossy(&buffer);
        stream.write_all(solve(input.to_string()).as_bytes()).unwrap();
    }
}


// #[cfg(test)]
// mod tests {
//     use super::*;

//     #[test]
//     fn it_works() {
//         let result = add(2, 2);
//         assert_eq!(result, 4);
//     }
// }
