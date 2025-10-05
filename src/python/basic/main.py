import math
import socket
import os
import sys

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


def solve(string):
    grid = Grid()
    for cell_id, value in enumerate(string):
        grid.set_value(math.floor(cell_id / 9), cell_id - ((math.floor(cell_id / 9) * 9)), int(value, 10))

    grid.solve()
    return grid.output()


if os.environ['SUDOKU']:
    print(solve(os.environ['SUDOKU']))
    sys.exit()


with socket.socket(socket.AF_UNIX, socket.SOCK_STREAM) as client:
    try:
        client.connect("/tmp/sudoku.sock")
        client.send(b"python:basic")

        while True:
            data = str(client.recv(81), 'ascii')
            
            client.send(bytes(solve(data), 'ascii'))
    except Exception as e:
        print("Exiting")