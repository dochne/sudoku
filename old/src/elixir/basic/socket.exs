defmodule UnixSocketClient do
  @socket_path "/tmp/sudoku.sock"

  def connect(server) do
    case :gen_tcp.connect({:local, @socket_path}, 0, [:binary, active: false]) do
      {:ok, socket} ->
        IO.puts("Connected to the server!")
        send_message(socket, server)
        handle_sudoku(socket)
        :gen_tcp.close(socket)
        IO.puts("Connection closed.")

      {:error, reason} ->
        IO.puts("Failed to connect: #{reason}")
    end
  end


  # # Connect to the Unix socket server
  # def connect_and_send_message(message) do
  #   case :gen_tcp.connect({:local, @socket_path}, 0, [:binary, active: false]) do
  #     {:ok, socket} ->
  #       IO.puts("Connected to the server!")
  #       send_message(socket, message)
  #       receive_response(socket)
  #       :gen_tcp.close(socket)
  #       IO.puts("Connection closed.")

  #     {:error, reason} ->
  #       IO.puts("Failed to connect: #{reason}")
  #   end
  # end

  # Send a message to the server
  defp send_message(socket, message) do
    :gen_tcp.send(socket, message <> "\n")
    # IO.puts("Sent message: #{message}")
  end

  # Receive and print the response from the server
  defp handle_sudoku(socket) do
    {state, response} = :gen_tcp.recv(socket, 0)
    if state != :ok do
      IO.puts("Failed to receive response: #{response}")
      exit("Failed")
    end

    # IO.puts("Received response: #{response}")
    send_message(socket, response)
    handle_sudoku(socket)

    # case :gen_tcp.recv(socket, 0) do
    #   {:ok, response} ->
    #     IO.puts("Received response: #{response}")

    #   {:error, reason} ->

    # end
  end
end

# Example usage:
UnixSocketClient.connect("elixir:basic")
# UnixSocketClient.connect_and_send_message("hello")
