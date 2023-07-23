# File.open()
# IO.puts("Hello world")

defmodule Sudoku do
  # def build_cells(cells_from_file) do
  #   build_cell_recursive(%{}, cells_from_file, 0)
  # end

  # def build_cell_recursive(list, from_file, cursor) do
  #   if (cursor >= 81) do
  #     list
  #   else
  #     row_index = floor(cursor / 9)
  #     column_index = rem(cursor, 9)

  #     # v = Enum.at(from_file, 0)

  #     value = 0
  #     row = Enum.at(from_file, row_index)
  #     if row != nil do
  #       col = Enum.at(row, column_index)
  #       if (col != nil && col != " ") do
  #         value = Integer.parse(col)
  #         # list = List.update_at(list, cursor, (&col))
  #       end
  #     end
  #     # IO.puts(Enum.join(list, ""))

  #     Map.put(list, cursor, value)
  #     # list[cursor] = value



  #     build_cell_recursive(list, from_file, cursor + 1)



  #     # Enum.at(from_file, 23)
  #     # IO.puts(row)
  #     # IO.puts(column)
  #     # IO.puts(Enum.at(Enum.at(from_file, row), column))
  #     # List.update_at(list, cursor, Enum.at(Enum.at(from_file, row), column))
  #   end
  end


  def build_col(remaining) do

  end


  def solve(cells) do
    Enum.each do cells, fn -> cell
      # p("Hello")
      IO.puts("Hello")
    end
    # IO.puts("Hello")
  end




  # def split(argument)
end

filename = hd(System.argv)

content = File.read!(filename)
  |> String.replace("\r", "")
  |> String.split("\n", trim: true)
  |> Enum.map(fn row -> String.pad_trailing(row, 9, " ") end)
  |> Enum.map(fn v -> String.split(v, "", trim: true) end)
  |> List.foldl([], fn row, acc -> acc ++ row end)
  |> Enum.map(fn v -> String.replace(v, " ", "0") end)
  |> Enum.map(fn v -> elem(Integer.parse(v), 0) end)
  |> Enum.reduce(Map.new(), fn v, acc -> Map.put(acc, map_size(acc), v) end)
  |> IO.inspect


response = Enum.to_list(0..80)
  |> Enum.map(fn v -> Map.get(content, v) end)
  |> Enum.chunk_every(9)
  |> Enum.map(fn row -> Enum.join(Enum.map(row, fn cell -> Integer.to_string(cell) end), "") end)
  |> Enum.join("\n")


IO.puts(response)

# cells = Sudoku.build_cells(content)
#  |> IO.inspect


#  IO.puts(cells)
# IO.puts(length(cells))
# Enum.at(cells, 1)
# IO.puts(cells)

# IO.puts(IO.gets(filename))

# Sudoku.myfunc()
