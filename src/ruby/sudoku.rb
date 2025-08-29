require 'socket'

RowMap = []
ColMap = []
ColCellMap = []
BoxCellMap = []
(0..80).each do |cell_id|
    RowMap[cell_id] = (cell_id / 9).floor
    ColMap[cell_id] = cell_id - (RowMap[cell_id] * 9)
    ColCellMap[cell_id] = 9.times.map{(_1 * 9) + ColMap[cell_id]}
    BoxCellMap[cell_id] = (0..2).map do | row_index |
        (0..2).map do |col_index|
            (((RowMap[cell_id] / 3).floor * 3 + row_index) * 9) + ((ColMap[cell_id] / 3).floor * 3) + col_index
        end
    end.flatten
end

class Grid
    def initialize(cells)
        @cells = cells
    end

    def valid(cell_id, value)
        return false if row_has?(cell_id, value)
        return false if box_has?(cell_id, value)
        return false if column_has?(cell_id, value)
        true
    end

    def row_has?(cell_index, value) @cells.slice(RowMap[cell_index]*9, 9).include?(value) end
    def column_has?(cell_index, value) ColCellMap[cell_index].any?{@cells[_1] == value} end
    def box_has?(cell_index, value) BoxCellMap[cell_index].any?{@cells[_1] == value} end

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

s = UNIXSocket.new("/tmp/sudoku.sock")
s.send "ruby:basic", 0

loop do
    data = s.read(81)
    exit if data.nil? || data.empty?
    grid = Grid.new(data.split("").map{_1.to_i})
    grid.solve()
    s.send grid.raw_output, 0
end
