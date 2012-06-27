<?php
require_once( '../inc.bootstrap.php' );
$json = file_get_contents( 'php://input' );

$response = Connect_Web_AjaxApplication::run( $json );

print $response;
?>