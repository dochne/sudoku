require "socket"

RowMap = Array(Int32).new(81, 0)
ColMap = Array(Int32).new(81, 0)
ColCellMap = Array(Array(Int32)).new(81, Array(Int32).new)
BoxCellMap = Array(Array(Int32)).new(81, Array(Int32).new)

(0..80).each do |cell_id|
    RowMap[cell_id] = (cell_id / 9).floor.to_i
    ColMap[cell_id] = cell_id - (RowMap[cell_id] * 9).to_i
    ColCellMap[cell_id] = 9.times.map{|n| (n * 9) + ColMap[cell_id]}.to_a
    BoxCellMap[cell_id] = (0..2).map do | row_index |
        (0..2).map do |col_index|
            ((((RowMap[cell_id] / 3).floor * 3 + row_index) * 9) + ((ColMap[cell_id] / 3).floor * 3) + col_index).to_i
        end
    end.flatten
end

# print "here2"
class Grid
    def initialize(cells : Array(Int32))
        @cells = cells
    end

    def valid(cell_id, value)
        return false if row_has?(cell_id, value)
        return false if box_has?(cell_id, value)
        return false if column_has?(cell_id, value)
        true
    end

    def row_has?(cell_index, value) @cells[RowMap[cell_index]*9, 9].includes?(value) end
    def column_has?(cell_index, value) ColCellMap[cell_index].any?{|v| @cells[v] == value} end
    def box_has?(cell_index, value) BoxCellMap[cell_index].any?{|v| @cells[v] == value} end

    def raw_output() @cells.join("") end
    def output() @cells.each_slice(9).map{_1.join("")}.join("\n") end

    def solve
        81.times do | cell_id |
            next if @cells[cell_id] != 0

            1.upto(9) do |num|
                if valid(cell_id, num)
                    @cells[cell_id] = num
                    return true if solve
                end
            end

            @cells[cell_id] = 0
            return nil
        end
        true
    end
end

def solve(string)
    grid = Grid.new(string.split("").map{|v| v.to_i})
    grid.solve
    grid.raw_output
end

if ENV["SUDOKU"] != ""
    print solve(ENV["SUDOKU"].chomp), "\n"
else
    s = UNIXSocket.new("/tmp/sudoku.sock")
    s.send "crystal:basic"

    buffer = Bytes.new(81)

    loop do
        begin
            s.read(buffer)
            exit if buffer.empty?
            s.send solve(String.new(buffer))
        rescue
            break
        end
    end
end
