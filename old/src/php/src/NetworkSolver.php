<?php

namespace Dolondro\Sudoku;

class NetworkSolver
{
    const SOCKET_PATH = '/tmp/sudoku.sock';

    public static function handle(string $name, callable $callable) 
    {
        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);

        if ($socket === false) {
            die("Socket creation failed: " . socket_strerror(socket_last_error()));
        }

        $result = socket_connect($socket, self::SOCKET_PATH);
        if ($result === false) {
            die("Socket connection failed: " . socket_strerror(socket_last_error($socket)));
        }

        socket_write($socket, $name, strlen($name));

        while (true) {
            $request = socket_read($socket, 81, PHP_NORMAL_READ);
    
            if ($request === false) {
                echo "Error in receiving data: " . socket_strerror(socket_last_error($socket)) . "\n";
                break;
            } elseif ($request === '') {
                echo "Connection closed by the server.\n";
                break;
            }

            $response = $callable($request);
            socket_write($socket, $response, 81);
        }

        socket_close($socket);
    }
}