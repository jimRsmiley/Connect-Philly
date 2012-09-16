<?php
require_once( '../inc.bootstrap.php' );

$json = file_get_contents('php://input');

Connect_SMS_CenterRequestApplication::run( $json );
