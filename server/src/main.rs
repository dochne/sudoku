// use tokio::net::{TcpListener, TcpStream};
use tokio::net::{UnixListener, UnixStream};
use tokio::io::{AsyncReadExt, AsyncWriteExt};
use tokio::time::{timeout, Duration, Instant};
use csv::ReaderBuilder;
use csv::StringRecord;
use std::io::BufReader;
use std::fs::File;
use rusqlite::{params, Connection, Result};
use std::path::Path;

// use std::time::{Duration, Instant};


async fn handle_connection(mut socket: UnixStream) {
    let mut buf = vec![0; 1024];
    
    let file = File::open("../examples/scratch/sudoku.csv").unwrap();
    let buffered_reader = BufReader::with_capacity(64 * 1024, file);

    let mut rdr = ReaderBuilder::new()
        .has_headers(true)
        .from_reader(buffered_reader);

    let client_name: String = match timeout(Duration::from_secs(30), socket.read(&mut buf)).await {
        Ok(Ok(bytes_read)) if bytes_read > 0 => {
            String::from_utf8_lossy(&buf[..bytes_read]).to_string()
        }
        _ => return
    };

    let split: Vec<&str> = client_name.split(":").collect();
    println!("{:#?}", split);
    let language = split.get(0).unwrap();
    let name = split.get(1).unwrap();
    println!("Language {}; Name {}", language, name);
    let mut solves = 0;
    let mut responses: i32 = 0;
    // // let mut start = 
    // let start = SystemTime::now().duration_since(UNIX_EPOCH).unwrap();
    let timeout_duration = Duration::from_secs(10); // Set timeout duration
    let start_time = Instant::now(); // Record the start time

    let mut record = StringRecord::new();

    loop {
        if start_time.elapsed() >= timeout_duration {
            println!("Timeout reached!");
            break;
        }

        rdr.read_record(&mut record).unwrap();
        let input = record.get(0).unwrap();
        if let Err(e) = socket.write_all(input.as_bytes()).await {
            println!("Failed to send message: {}", e);
            break;
        }

        match timeout(Duration::from_secs(30), socket.read(&mut buf)).await {
            Ok(Ok(bytes_read)) if bytes_read > 0 => {
                let received = String::from_utf8_lossy(&buf[..bytes_read]);
                if received == record.get(1).unwrap() {
                    solves+= 1;
                }
                responses+= 1;
                
            }
            _ => break,
        }
    }
    let duration = start_time.elapsed();
    let mut per_second = 0.0;
    println!("Elapsed: {:#?}", duration);
    println!("Solves: {:#?}", solves);
    println!("Responses: {:#?}", responses);
    
    if solves > 0 {
        per_second = f64::from(solves) / duration.as_secs_f64();
        println!("Solves per second: {:#?}", per_second);
    }

    let conn = Connection::open("../benchmark.db").unwrap();
    conn.execute(
        "INSERT INTO execution (language, name, solves, duration, per_second) VALUES (?1, ?2, ?3, ?4, ?5)
         ON CONFLICT(language, name) DO UPDATE SET solves = excluded.solves, duration = excluded.duration, per_second=excluded.per_second",
        params![language, name, solves, duration.as_secs_f64(), per_second],
    ).unwrap();

}

#[tokio::main]
async fn main() -> Result<(), Box<dyn std::error::Error>> {
    // let listener = TcpListener::bind("127.0.0.1:8080").await?;
    let socket_path = "/tmp/sudoku.sock";

    if Path::new(socket_path).exists() {
        std::fs::remove_file(socket_path)?;
    }

    let listener = UnixListener::bind(socket_path)?;
    let conn = Connection::open("../benchmark.db")?;

    // Run the migration to create the table if it doesn't exist
    conn.execute("
        CREATE TABLE IF NOT EXISTS execution (
            language TEXT NOT NULL,
            name TEXT NOT NULL DEFAULT '0',
            solves INTEGER NOT NULL,
            duration REAL NOT NULL,
            per_second REAL NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (language, name)
        )
    ",
    []
    )?;

    loop {
        let (socket, _) = listener.accept().await?;
        tokio::spawn(async move {
            handle_connection(socket).await;
        });
    }
}

// With no solving, we max out at:
// Elapsed: 10.000016875s
// Solves: 1,281,859
// Solves per second: 128,185.68368665878