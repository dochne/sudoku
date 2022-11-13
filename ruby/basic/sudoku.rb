#!/usr/bin/env ruby

class Grid

    def initialize()
        @cells = []
        for row_i in 0..8
            @cells[row_i] = []
            for col_i in 0..8
                @cells[row_i][col_i] = 0
            end
        end
    end
    
    def set_value(row, col, value)
        @cells[row][col] = value
    end

    def is_valid(row_index, col_index, value)
        for row_loop_i in 0..8
            if @cells[row_loop_i][col_index] == value then
                return false
            end
        end

        for col_loop_i in 0..8
            if @cells[row_index][col_loop_i] == value then
                return false
            end
        end
        
        row_start = (row_index / 3).floor() * 3
        col_start = (col_index / 3).floor() * 3

        for row_loop_i in 0..2
            for col_loop_i in 0..2
                if @cells[row_start + row_loop_i][col_start + col_loop_i] == value then
                    return false
                end
            end
        end

        return true
    end

    def output()
        @cells.each do |row|
            row.each do |col|
                print(col)
            end
            print("\n")
        end
    end

    def solve() 
        @cells.each_with_index do |row, rowIndex| 
            @cells[rowIndex].each_with_index do |col, colIndex|
                if @cells[rowIndex][colIndex] == 0 then
                    for value in 1..9 
                        if self.is_valid(rowIndex, colIndex, value) then
                            self.set_value(rowIndex, colIndex, value)

                            if self.solve() then
                                return true
                            end
                        end
                    end

                    self.set_value(rowIndex, colIndex, 0)
                    return false
                end
            end
        end

        return true
    end

end


grid = Grid.new()

filename = ARGV[0]
content = File.read(filename).gsub(/\r/, "").split("\n")

# for row_i, row in enumerate(lines):
    content.each_with_index do | row, row_i |
        if (row_i < 9) then
            row.split("").each_with_index do | value, col_i |
                grid.set_value(row_i, col_i, value == ' ' ? 0 : Integer(value))
            end
        end
    end
# end

grid.solve()
grid.output()

    # for col in range(len(row)):
    #     try:
    #         grid.set_value(row_i, col_i, int(row[col_i], 10))
    #     except ValueError as e:
    #         pass

