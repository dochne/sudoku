require "json"
require "date"

filename = ARGV[0]

constraints = [511] * 27

$bin_map = Hash.new
$inverse_bin_map = Hash.new
sum = 0
(1..9).each do |n|
    value = (1 << n - 1)
    sum |= value
    $bin_map[n] = value;
    $inverse_bin_map[value] = n
end

$cell_constraints = Hash.new
(0...81).each do |cell_id|
    row_id = (cell_id / 9).floor
    col_id = cell_id % 9
    block_id = (row_id / 3).floor * 3 + (col_id / 3).floor
    $cell_constraints[cell_id] = [0 + row_id, 9 + col_id, 18 + block_id]
end

$number_map = []
(0..512).each do |n|
    numbers = []
    (1..9).each do |i|
        if n & $bin_map[i] != 0 
            numbers << $bin_map[i];
        end
    end
    $number_map << numbers
end

$number_total_map = {}
(0...512).each do |n|
    count = 0
    v = n
    while v > 0 do
        count += 1
        v = v & (v - 1)
    end
    $number_total_map[n] = count
end

grid = File
    .read(filename)
    .gsub(/\r/, "")
    .split("\n")
    .map{_1.split("").map(&:to_i)}
    .map{_1.fill(0, _1.size, 9 - _1.size)}
    .flatten

(0...81).each do |cell_id|
    next if grid[cell_id] == 0

    $cell_constraints[cell_id].each do |constraint_id|
        constraints[constraint_id] ^= $bin_map[grid[cell_id]]
    end
end

def solve(grid, constraints)
    # p("Calling solve")
    # p(grid.each_with_index.to_a)
    lowest_cell = 0
    grid.size.times do |cell_id|

    # grid.each_with_index do |cell, cell_id|
        next if grid[cell_id] != 0
        length = $number_total_map[
            constraints[$cell_constraints[cell_id][0]] & 
            constraints[$cell_constraints[cell_id][1]] &
            constraints[$cell_constraints[cell_id][2]]
        ]
        if (length == 0)
            # If something has the length of 0, then we don't have any valid constraints
            # for one of our numbers so this is a wash
            return false
        end
        if lowest_cell == 0 || lowest_cell[1] > length
            # p("Lowest_cell being set #{lowest_cell} - #{length}", "\n")
            lowest_cell = [cell_id, length]
            if length == 1 
                # p("Breaking due to length")
                break
            end
        end
    end

    # p(lowest_cell)

    # empty_cells = grid
    #     .each_with_index
    #     .filter{|cell, cell_id| cell == 0}
    #     .map do |_, cell_id|
    #         [
    #             cell_id,
                
    #         ]
    #     end
    #     .sort_by{_2}
    
    # if (empty_cells.size == 0)
    #     p("Returning true 'cause no empty-cells", grid)
    # end
    return true if lowest_cell == 0

    cell_id = lowest_cell[0]
    value = constraints[$cell_constraints[cell_id][0]] &
        constraints[$cell_constraints[cell_id][1]] & 
        constraints[$cell_constraints[cell_id][2]] 

    $number_map[value].each do |num|
        grid[cell_id] = $inverse_bin_map[num]
        constraints[$cell_constraints[cell_id][0]] ^= num
        constraints[$cell_constraints[cell_id][1]] ^= num
        constraints[$cell_constraints[cell_id][2]] ^= num
        return grid if solve(grid, constraints)
        constraints[$cell_constraints[cell_id][0]] |= num
        constraints[$cell_constraints[cell_id][1]] |= num
        constraints[$cell_constraints[cell_id][2]] |= num
        grid[cell_id] = 0
    end
    return false
end


start = DateTime.now
grid = solve(grid, constraints)
duration = ((DateTime.now - start).to_f * 1_000_000)
p("Failed") if !grid

print(JSON({
    "time" => duration * 1000,
    "output" => grid.each_slice(9).map(&:join).map(&:to_s).join("\n")
}));



# grid.each_slice(9).map(&:join).each do |row|
#     print(row, "\n")
# end

# p(grid)
# p(newv)
# p(cell_links)

# p(content)
