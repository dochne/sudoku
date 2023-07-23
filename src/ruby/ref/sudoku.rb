
class Cell
    def initialize(value, cell_id)
        @value = value
        @cell_id = cell_id
    end

    def set(value)
        @value = value
    end

    # def valid(num)
    #     !@links.map(&:value).include?(num)
    # end

    def links(cells)
        @links = cells
    end

    def value
        @value
    end

    def cell_id
        @cell_id
    end
end

class Grid
    def initialize(grid_array)
        @cells = (0..80).map do | cell_id |
            row, col = cell_to_row_and_col(cell_id)
            Cell.new(grid_array[row][col] || 0, cell_id)
        end

        @links = (0..80).map do |cell_id|
            row_id, col_id = cell_to_row_and_col(cell_id)
            (row(row_id) + column(col_id) + box(row_id, col_id)).uniq
        end

        row_id, col_id = cell_to_row_and_col(0)
    end

    def valid(cell_id, value)
        !@links[cell_id].any?{|cell| cell.value == value}
    end

    def row(index)
        @cells.slice(index*9, 9)
    end

    def column(index)
        @cells.map.with_index do | cell, cell_id |
            if (cell_id % 9) == index 
                cell
            else
                nil
            end
            # filter({|row| row[index]}
        end.compact
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
            (0..2).map do | col_index |
                @cells[row_and_col_to_cell(start_row_index + row_index, start_col_index + col_index)]
            end
        end.flatten
    end

    def output
        @cells.each_slice(9).map{|row| row.map(&:value).join("")}.join("\n")
    end

    def solve

        @cells.each_with_index do |cell, cell_id|            
            next if cell.value != 0

            (1..9).each do |num|
                if valid(cell_id, num)
                    cell.set(num)
                    return true if solve
                end
            end

            cell.set(0)
            return nil
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

grid.solve

# print grid.cells.map(&:value)
# grid.solve
print grid.output
