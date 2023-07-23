const std = @import("std");

pub fn main() void {
    // std.debug.print("Hello, {s}!\n", .{"World"});
    // [5]u8{ 'h', 'e', 'l', 'l', 'o' };
    // var _my = [81]u8{};

    var lol = content("examples/1_input.txt");



}



fn content(filename: string) u8{81} {
    var file = try std.fs.cwd().openFile("foo.txt", .{});
    defer file.close();

    var buf_reader = std.io.bufferedReader(file.reader());
    var in_stream = buf_reader.reader();

    var output_buffer = u8{81};
    var buf: [1024]u8 = undefined;
    while (try in_stream.readUntilDelimiterOrEof(&buf, '\n')) |line| {
        // do something with line...
        output_buffer += line;
    }
    return output_buffer;
}