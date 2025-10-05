package main

import (
	"log"
	"os"
	"strconv"
	"math"
	"strings"
	"net"
)

func solve(text string) string {
	grid := Grid{}
	for col, char := range text {
		char := string(char)

		value := 0
		if char != " " {
			value = must(strconv.Atoi(char))
		}

		colFloat := float64(col)
		grid.SetValue(
			int(math.Floor(colFloat / 9)),
			int(colFloat) % 9,
			value,
		)
	}
	
	grid.Solve()
	return grid.Output()
}

func must[T any](v T, err error) T {
	if err != nil {
		log.Fatal(err)
	}
	return v
}

func main() {

	if os.Getenv("SUDOKU") != "" {
		print(solve(os.Getenv("SUDOKU")), "\n")
		os.Exit(0)
	}
	
	conn := must(net.Dial("unix", "/tmp/sudoku.sock"))
	defer conn.Close()

	must(conn.Write([]byte("go:basic")))

	buf := make([]byte, 81)

	for {
		var err error
		_, err = conn.Read(buf)
		if err != nil {
			os.Exit(0)
		}

		_, err = conn.Write([]byte(solve(strings.TrimSpace(string(buf)))))
		if err != nil {
			os.Exit(0)
		}
	}
}

type Grid struct {
	cells [9][9]int
}

func (g *Grid) SetValue (row int, col int, value int) {
	g.cells[row][col] = value
}

func (g Grid) IsValid(row int, col int, value int) bool {
	for rI := 0; rI < 9; rI++ {
		if g.cells[rI][col] == value {
			return false
		}
	}

	for cI := 0; cI < 9; cI++ {
		if g.cells[row][cI] == value {
			return false
		}
	}

	rowStart:= int(math.Floor(float64(row) / 3) * 3)
	colStart:= int(math.Floor(float64(col) / 3) * 3)

	for rI := 0; rI < 3; rI++ {
		for cI := 0; cI < 3; cI++ {
			if g.cells[rowStart + rI][colStart + cI] == value {
				return false
			}
		}
	}

	return true
}

func (g *Grid) Solve() bool {
	for rowIndex := 0; rowIndex < 9; rowIndex++ {
		for colIndex :=0; colIndex < 9; colIndex++ {
			if g.cells[rowIndex][colIndex] == 0 {
				for value :=1; value < 10; value++ {
					if g.IsValid(rowIndex, colIndex, value) {
						g.SetValue(rowIndex, colIndex, value)
						if g.Solve() {
							return true
						}
					}
				}
				g.SetValue(rowIndex, colIndex, 0)
				return false
			}
		}
	}

	return true
}

func (g Grid) Output() string {
	var sb strings.Builder

	for rowIndex := 0; rowIndex < 9; rowIndex++ {
		for colIndex := 0; colIndex < 9; colIndex++ {
			sb.WriteString(strconv.FormatInt(int64(g.cells[rowIndex][colIndex]), 10) + "")
		}
	}

	return sb.String()
}