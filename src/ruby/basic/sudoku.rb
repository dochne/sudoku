require 'socket'

class Grid
    def initialize(cells)
        @cells = cells
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

    def raw_output
      @cells.map(&:join).join
    end
    
    def output()
        @cells
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


def solve(string)
    array = string.chomp.scan(/.{9}/).map do |row|
      row.split("").map(&:to_i)
    end

    grid = Grid.new(array)
    grid.solve
    grid.raw_output
end

if ENV['SUDOKU']
    print solve(ENV['SUDOKU']), "\n"
    exit(0)
end

s = UNIXSocket.new("/tmp/sudoku.sock")
s.send "ruby:basic", 0

loop do
    data = s.read(81)
    exit if data.nil? || data.empty?
    s.send solve(data), 0
end

