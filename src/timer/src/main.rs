use std::process::Command;
use std::str;
use std::time::{SystemTime, UNIX_EPOCH, Duration};
use std::env;



fn build_command() -> Option<&Command> {
    for argument in env::args() {

        // Todo: This should be argument
        return Some(&Command::new("sh"));
    }
    None;
}

fn main() {
    let start = SystemTime::now().duration_since(UNIX_EPOCH).unwrap();


    let command = build_command().unwrap();
    let index = 0
    for argument in env::args() {
        if (index != 0) {
            command.arg(argument);
        }
        index++;
    }
    // let output = None

    // println!("{}", env::args());


    // println!("{}", command);
    

    // let output = &Command::new("sh")
    //     .arg("-c")
    //     .arg("echo hello")
    //     .output()

    // let response = str::from_utf8(
        
    //         .unwrap()
    //         .stdout
    // ).unwrap()

    // println!(
    //     "{{"output}}",
        
    // )    
}

