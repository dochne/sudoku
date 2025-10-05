const { connect } = require('http2');
const net = require('net');


class Grid {
    constructor(array) {
        this.cells = array;
    }

    isValid(row, col, value) {
        for (let rowI = 0; rowI < 9; rowI++) {
            if (this.cells[rowI][col] === value) {
                return false;
            }
        }

        for (let colI = 0; colI < 9; colI++) {
            if (this.cells[row][colI] === value) {
                return false;
            }
        }

        const rowStart = Math.floor(row / 3) * 3;
        const colStart = Math.floor(col / 3) * 3;

        for (let rowI = 0; rowI < 3; rowI++) {
            for (let colI = 0; colI < 3; colI++) {
                if (this.cells[rowStart + rowI][colStart + colI] === value) {
                    return false;
                }
            }
        }

        return true;
    }

    solve() {
        for (let rowIndex = 0; rowIndex < 9; rowIndex++) {
            for (let colIndex = 0; colIndex < 9; colIndex++) {
                if (this.cells[rowIndex][colIndex] === 0) {
                    for (let value = 1; value < 10; value++) {
                        if (this.isValid(rowIndex, colIndex, value)) {
                            this.cells[rowIndex][colIndex] = value;
                            if (this.solve()) {
                                return true;
                            }
                        }
                    }
                    this.cells[rowIndex][colIndex] = 0;
                    return false;
                }
            }
        }

        return true;
    }

    rawOutput() {
        return this.cells.map(v => v.join("")).join("");
    }
}


function solve(string) {
    let slices = [];
    for (let i = 0; i < string.length; i += 9) {
        slices.push(string.slice(i, i + 9).split("").map(v => Number(v)));
    }

    let grid = new Grid(slices);
    grid.solve();
    return grid.rawOutput();    

}


if (process.env.SUDOKU) {
    console.log(solve(process.env.SUDOKU));
    process.exit(0);
}


function connectToSocket(path) {
  return new Promise((resolve, reject) => {
    const client = net.createConnection(path);
    client.on('connect', () => resolve(client));
    client.on('error', (err) => reject(err));
  });
}



(async() => {
    const client = await connectToSocket("/tmp/sudoku.sock");
    client.write("js:basic");
    for await (const chunk of client) {
      client.write(solve(chunk.toString()));
    }

})();


// const client = net.createConnection("/tmp/sudoku.sock", () => {
//   // 2. Send data to the server
//   const message = 'Hello from the Node.js client!';
//   client.write(message);
//   console.log(`Sent to server: ${message}`);
// });


// // // New lines
// // const rows = data.split("\n");
// // for (let row = 0; row < rows.length; row++) {
// //     const cols = rows[row].split("");
// //     for (let col = 0; col < cols.length; col++) {
// //         const value = parseInt(rows[row][col], 10);
// //         if (!isNaN(value)) {
// //             grid.setValue(row, col, value);
// //         }
// //     }
// // }

// // // const start = Date.now();
// // const start = process.hrtime.bigint();
// // grid.solve();

// // const duration = Number(process.hrtime.bigint() - start) / 1_000;
// // // const duration = (Date.now() - start) * 1_000;

// // console.log("{\"time\":" + duration + ",\"output\":\"");
// // grid.print();
// // console.log("\"}");
