

class Grid
    def initialize(grid_array)
        @cells = (0..80).map do | cell_id |
            value = cell_to_row_and_col(cell_id)
            row = value[0]
            col = value[1]
            grid_array[row][col] || 0
        end
    end

    def valid(cell_row_index, cell_col_index, value)
        return false if row(cell_row_index).include?(value)
        return false if column(cell_col_index).include?(value)
        return false if box(cell_row_index, cell_col_index).include?(value)
        true
    end

    def row(index)
        @cells.slice(index*9, 9)
    end

    def column(index)
        @cells.map{|row| row[index]}
    end

    def row_and_col_to_cell(row, col)
        (row * 9) + col
    end

    def cell_to_row_and_col(cell_id)
        [(cell_id / 9).floor, (cell_id % 9).floor]
    end

    def box(cell_row_index, cell_col_index)
        start_row_index = (cell_row_index / 3).floor * 3
        start_col_index = (cell_col_index / 3).floor * 3

        box_items = (0..2).map do | row_index |
            (0..2).map{|col_index| @cells[start_row_index + row_index][start_col_index + col_index]}
        end.flatten

    end

    def output
        @cells.map{|row| row.join("")}.join("\n")
    end

    def solve
        @cells = (0..80).map do | cell_id |
            value = cell_to_row_and_col(cell_id)
            row = value[0]
            col = value[1]
        
                next if @cells[row][col] != 0

                (1..9).each do |num|
                    if valid(row, col, num)
                        @cells[row][col] = num
                        return true if solve
                    end
                end

                @cells[row][col] = 0
                return nil
            end
        end
        true
    end

    def cells
        @cells
    end
end


filename = ARGV[0]
content = File.read(filename).gsub(/\r/, "").split("\n")

grid = Grid.new(content.map do | row |
    row.split("").map{| value | value == ' ' ? nil : Integer(value)} 
end)

print grid.cells
# grid.solve
# print grid.output
