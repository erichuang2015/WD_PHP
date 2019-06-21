<?php
	header('Content-Type: application/json; charset=utf-8');
	ob_clean();
	echo json_encode($_SESSION);
	exit;