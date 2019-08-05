package main

import (
	"log"
	"os"
	"bufio"
	//"strconv"
	//"fmt"
	//"strconv"
	"strconv"
	"math"
	"fmt"
)

/**
 * We'll want a function that keeps track of
 */
func main() {
	// So, this is obviously going to be a lot less elegant than PHP because I'm not as confident in what I'm doing :P

	file, err := os.Open(os.Args[1])
	if err != nil {
		log.Fatal(err)
	}
	defer file.Close()

	grid := Grid{}

	scanner := bufio.NewScanner(file)
	row := 0
	for scanner.Scan() {
		line := scanner.Text()

		for col, char := range line {
			char := string(char)

			value := 0
			if char != " " {
				value, err = strconv.Atoi(char)
				if err != nil {
					log.Fatal(err)
				}
			}

			grid.SetValue(row, col, value)

		}
		row++
	}

	if grid.Solve() {
		grid.Print()
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


func (g Grid) Print() {
	for rowIndex := 0; rowIndex < 9; rowIndex++ {
		for colIndex := 0; colIndex < 9; colIndex++ {
			fmt.Print(strconv.FormatInt(int64(g.cells[rowIndex][colIndex]), 10) + " ")
		}
		fmt.Print("\n")
	}
}