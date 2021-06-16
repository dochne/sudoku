print(ARGS)

points = []
open(ARGS[1], "r") do io
    global points;
    points = read(io)
end

struct Grid
	cells::UInt8[9][9]
end

for x = 1:length(points)
    print(Char(points[x]))
end

# println()
# println(length(points))
# for x in [1 .. length(points)]
#     println(points[x])
