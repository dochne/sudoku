<?php

$sharedMemory = shmop_open(ftok(__FILE__, chr(0)), "c", 0644, 100);
$pid = pcntl_fork();

if ($pid == -1) {
    die("Fork failure");
} else if ($pid) {

    while (pcntl_waitpid(0, $status) != -1)
    {
        $data = shmop_read($sharedMemory, 0, 100);
        echo "Received: " . $data . "\n";
        shmop_delete($sharedMemory);
    }
} else {
    shmop_write($sharedMemory, "100 bytes of data goes here", 0);
}
