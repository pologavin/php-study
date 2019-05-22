<?php
/**
 *
 * User: gaojun<godwin.gao@huolala.cn>
 * Date: 2019/5/15
 * Time: 下午7:56
 */
require_once 'task.php';
require_once 'scheduler.php';
require_once 'SystemCall.php';

function server($port) {
    echo "Starting server at port $port...\n";

    $socket = @stream_socket_server("tcp://localhost:$port", $errNo, $errStr);
    if (!$socket) throw new Exception($errStr, $errNo);

    stream_set_blocking($socket, 0);

    while (true) {
        yield waitForRead($socket);
        $clientSocket = stream_socket_accept($socket, 0);
        yield newTask(handleClient($clientSocket));
    }
}

function handleClient($socket) {
    yield waitForRead($socket);
    $data = fread($socket, 8192);

    $msg = "Received following request:\n\n$data";
    $msgLength = strlen($msg);

    $response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;

 
    yield waitForWrite($socket);
    fwrite($socket, $response);
 
    fclose($socket);
}
 
$scheduler = new Scheduler;
$scheduler->newTask(server(8000));
$scheduler->run();
