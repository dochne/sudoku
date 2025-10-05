use std::os::unix::net::UnixStream;
use std::io::{Read, Write};
use std::process;

const SOCKET_PATH: &str = "/tmp/sudoku.sock";

pub fn run(name: String, solve: fn(String) -> String) {
    let mut stream = UnixStream::connect(SOCKET_PATH).unwrap();
    let mut buffer = [0;81];
    stream.write_all(name.as_bytes()).unwrap();

    loop {
        stream.read_exact(&mut buffer).unwrap_or_else(|_| process::exit(0));
        let input = String::from_utf8_lossy(&buffer);
        stream.write_all(solve(input.to_string()).as_bytes()).unwrap();
    }
}