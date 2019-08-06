import { readFileSync } from "fs";

class Grid {
    private cells: number[][] = new Array(9);

    public constructor() {
        for (let x = 0; x < this.cells.length; x++) {
            // I originally attempted this:
            // new Array(9).fill(new Array(9).fill(0))
            // But it will only create a single object and populate all 9 rows with it
            this.cells[x] = new Array(9).fill(0);
        }
    }

    public setValue(row: number, col: number, value: number): void {
        this.cells[row][col] = value;
    }

    public print() {
        for (const row of this.cells) {
            console.log(row.join(" "));
        }
    }

    public isValid(row: number, col: number, value: number): boolean {
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

    public solve() {
        for (let rowIndex = 0; rowIndex < 9; rowIndex++) {
            for (let colIndex = 0; colIndex < 9; colIndex++) {
                if (this.cells[rowIndex][colIndex] === 0) {
                    for (let value = 1; value < 10; value++) {
                        if (this.isValid(rowIndex, colIndex, value)) {
                            this.setValue(rowIndex, colIndex, value);
                            if (this.solve()) {
                                return true;
                            }
                        }
                    }
                    this.setValue(rowIndex, colIndex, 0);
                    return false;
                }
            }
        }

        return true;
    }
}

// const filename = "../examples/1_input.txt";
const args = Array.prototype.slice.call(process.argv);
const filename = args.pop();

const data = readFileSync(filename).toString();

const grid = new Grid();

// New lines
const rows = data.split("\n");
for (let row = 0; row < rows.length; row++) {
    const cols = rows[row].split("");
    for (let col = 0; col < cols.length; col++) {
        const value = parseInt(rows[row][col], 10);
        if (!isNaN(value)) {
            grid.setValue(row, col, value);
        }
    }
}

grid.solve();
grid.print();
