import sys
import math
import time
import json


class Grid:
    def __init__(self):
        self.cells = dict()

        for row_i in range(9):
            self.cells[row_i] = dict()
            for col_i in range(9):
                self.cells[row_i][col_i] = 0

    def set_value(self, row, col, value):
        self.cells[row][col] = value

    def is_valid(self, row_index, col_index, value):
        for row_loop_i in range(9):
            if self.cells[row_loop_i][col_index] == value:
                return False
        for col_loop_i in range(9):
            if self.cells[row_index][col_loop_i] == value:
                return False

        row_start = math.floor(row_index / 3) * 3
        col_start = math.floor(col_index / 3) * 3

        for row_loop_i in range(3):
            for col_loop_i in range(3):
                if self.cells[row_start + row_loop_i][col_start + col_loop_i] == value:
                    return False

        return True

    def output(self):
        return ''.join(str(self.cells[row][col]) for row in self.cells for col in self.cells[row])

        # for row in self.cells:
        #     # print(row.join(""))
        #     for col in self.cells[row]:
        #         # self.cells.join(",")
        #         print(self.cells[row][col], "", end='')
            # print("", end='')

    def solve(self):
        for rowIndex in self.cells:
            for colIndex in self.cells[rowIndex]:
                if self.cells[rowIndex][colIndex] == 0:
                    for value in range(1, 10):
                        if self.is_valid(rowIndex, colIndex, value):
                            self.set_value(rowIndex, colIndex, value)

                            if self.solve():
                                return True

                    self.set_value(rowIndex, colIndex, 0)
                    return False
        return True


filename = sys.argv.pop()

f = open(filename, "r")
contents = f.read()
lines = contents.splitlines()

grid = Grid()

for row_i, row in enumerate(lines):
    for col_i in range(len(row)):
        try:
            grid.set_value(row_i, col_i, int(row[col_i], 10))
        except ValueError as e:
            pass

start_time = time.time()  # Start the timer
if grid.solve():
    duration = time.time() - start_time    # End the timer
    print("{\"time\":" + str(duration) + ",\"output\":\"" + grid.output() + "\"}");
else:
    print("Unsolvable")
