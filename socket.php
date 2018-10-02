<?php 
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

include_once("include/header.php");
include_once(APP_PATH.'include/phpsocket.io/autoload.php'); 
include_once(APP_PATH.'class/design.class.php');				//樣板模組
include_once(APP_PATH.'include/main.php');						//核心
include_once(APP_PATH.'class/webSetting.class.php');			//網站設定


if(php_sapi_name() != "cli"){//only run in command line mode
    exit;
}


$io = new SocketIO(2020);


$io->on('workerStart', function()use($io) {
    $inner_http_worker = new Worker('http://0.0.0.0:9191');
    $inner_http_worker->onMessage = function($http_connection, $data)use($io){
        if(!isset($_GET['msg'])) {
            return $http_connection->send('fail, $_GET["msg"] not found');
        }
        $io->emit('chat message', $_GET['msg']);
        $http_connection->send('ok');
    };
    $inner_http_worker->listen();
});

$io->on('connection', function($socket)use($io){
  	echo "connection ".date("Y-m-d a h:i:s")." \n";
// when the client emits 'new message', this listens and executes
	$socket->on('new message', function ($data)use($socket){
  		echo "new message\n";
	    // we tell the client to execute 'new message'
	    $socket->broadcast->emit('new message', array(
	        'username'=> $socket->username,
	        'message'=> $data
	    ));
	});

	// when the client emits 'add user', this listens and executes
	$socket->on('add user', function ($username) use($socket){
	    global $usernames, $numUsers;
	    // we store the username in the socket session for this client
	    $socket->username = $username;
	    // add the client's username to the global list
	    $usernames[$username] = $username;
	    ++$numUsers;
	    $socket->addedUser = true;
	    $socket->emit('login', array( 
	        'numUsers' => $numUsers
	    ));
	    // echo globally (all clients) that a person has connected
	    $socket->broadcast->emit('user joined', array(
	        'username' => $socket->username,
	        'numUsers' => $numUsers
	    ));
	});

	// when the client emits 'typing', we broadcast it to others
	$socket->on('typing', function () use($socket) {
	    $socket->broadcast->emit('typing', array(
	        'username' => $socket->username
	    ));
	});

	// when the client emits 'stop typing', we broadcast it to others
	$socket->on('stop typing', function () use($socket) {
	    $socket->broadcast->emit('stop typing', array(
	        'username' => $socket->username
	    ));
	});

	// when the user disconnects.. perform this
	$socket->on('connect', function () use($socket) {
	    global $usernames, $numUsers;
	    // remove the username from global usernames list
	    if($socket->addedUser) {
	        unset($usernames[$socket->username]);
	        --$numUsers;

	       // echo globally that this client has left
	       $socket->broadcast->emit('user left', array(
	           'username' => $socket->username,
	           'numUsers' => $numUsers
	        ));
	    }
	});

	// when the user disconnects.. perform this
	$socket->on('disconnect', function () use($socket) {
	    global $usernames, $numUsers;
	    // remove the username from global usernames list
	    if($socket->addedUser) {
	        unset($usernames[$socket->username]);
	        --$numUsers;

	       // echo globally that this client has left
	       $socket->broadcast->emit('user left', array(
	           'username' => $socket->username,
	           'numUsers' => $numUsers
	        ));
	    }
	});
 //    $socket->on('disconnect', function () use($socket) {
	//   echo "disconnect\n";
	// });
});


Worker::runAll();