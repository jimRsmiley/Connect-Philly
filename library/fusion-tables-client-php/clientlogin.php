<?php

include_once "constants.php";

class ClientLogin {
  public static function getAuthToken($username, $password) {
      
      if( empty($username) ) {
          throw new Exception( 'username cannot be empty' );
      }
      
      if( empty( $password ) ) {
          throw new Exception( 'password cannot be empty' );
      }
    $clientlogin_curl = curl_init();
    curl_setopt($clientlogin_curl,CURLOPT_URL,'https://www.google.com/accounts/ClientLogin');
    curl_setopt($clientlogin_curl, CURLOPT_POST, true); 
    curl_setopt($clientlogin_curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt ($clientlogin_curl, CURLOPT_POSTFIELDS,
	    "Email=".$username."&Passwd=".$password."&service=fusiontables&accountType=GOOGLE");
    curl_setopt($clientlogin_curl,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($clientlogin_curl,CURLOPT_RETURNTRANSFER,1);
    $token = curl_exec($clientlogin_curl);
    
    if( $error = curl_error($clientlogin_curl) ) {
        throw new Zend_Exception( "ERROR: ".$error );
    }
    
    curl_close($clientlogin_curl);
    
    $token_array = explode("=", $token);
    
    if( preg_match( "/BadAuthentication/", $token_array[1] ) ) {
        throw new Zend_Exception( "Client Login failed" );
    }
    
    $token = str_replace("\n", "", $token_array[3]);
    return $token;
  }
}


class FTClientLogin {
  function __construct($token) {
    $this->token = $token;
  }
  
  function query($query, $gsessionid = null, $recursedOnce = false) {
    
    $url = URL;
   	$query =  "sql=".urlencode($query);
   	
    $fusiontables_curl=curl_init();
    if(preg_match("/^select|^show tables|^describe/i", $query)) { 
   	  $url .= "?".$query;
      if($gsessionid) { $url .= "&gsessionid=$gsessionid"; }
      curl_setopt($fusiontables_curl,CURLOPT_HTTPHEADER, array("Authorization: GoogleLogin auth=".$this->token));
    
    } else {
      if($gsessionid) { $url .= "?gsessionid=$gsessionid"; }
      
      //set header
      curl_setopt($fusiontables_curl,CURLOPT_HTTPHEADER, array( 
        "Content-length: " . strlen($query), 
        "Content-type: application/x-www-form-urlencoded", 
        "Authorization: GoogleLogin auth=".$this->token         
      )); 
      
      //set post = true and add query to postfield
      curl_setopt($fusiontables_curl,CURLOPT_POST, true);
      curl_setopt($fusiontables_curl,CURLOPT_POSTFIELDS,$query); 
    }
    
    curl_setopt($fusiontables_curl,CURLOPT_URL,$url);
    curl_setopt($fusiontables_curl,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($fusiontables_curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($fusiontables_curl,CURLOPT_SSL_VERIFYPEER,false);
    $result = curl_exec($fusiontables_curl);
    
    if( $error = curl_error($fusiontables_curl) ) {
        throw new Zend_Exception( "Curl error: ".$error );
    }
    
    curl_close($fusiontables_curl);
    
    //If the result contains moved Temporarily, retry
    if (strpos($result, '302 Moved Temporarily') !== false) {
      preg_match("/(gsessionid=)([\w|-]+)/", $result, $matches);
      
      if (!$matches[2]) { return false; }

      if ($recursedOnce === false) {
        return $this->query($url, $matches[2], true);
      }
      return false;
    }

    return $result;
  }
}

?>
