<?php
  namespace App\Controllers;
  use \PDO as PDO;
  use \App\PDO_start as PDO_start;
  use \App\Controllers\Get_DB as Get_DB;
  use \App\Controllers\Post_DB as Post_DB;
  
  class Auth
  {
    private $domain, $my_ip, $cookie_time, $connector, $post_db;    


    function __construct( string $domain, int $my_ip, int $cookie_time )
    {
      $this->domain = $domain;
      $this->my_ip = $my_ip;
      $this->cookie_time = $cookie_time;
      $this->connector = new Get_DB( 'users' );
      $this->post_db = new Post_DB( 'users' );
    }


    function logoff()
    {
      setcookie( 'user_login', '', time() -1, '/', '.' . $this->domain );
      setcookie( 'hash', '', time() -1, '/', '.' . $this->domain );
      header( 'Location: /' );
      die( 'fuckoff' );
    }


    function connect()
    {
      switch (true) {
        case !isset( $_POST['login'] ):
        case !isset( $_POST['password'] ):
        case empty( $_POST['login'] ):
        case empty( $_POST['password' ]):
        case preg_match( '/[^\w\-]/iu', $_POST['login'] ):
          die( 'recheck' );
        default:
          $db = $this->connector->getUser([ $_POST['login'] ]);
          $this->post_db->updateHash([ 'user_hash' => '', 'user_login' => $_POST['login'] ]);
          break;
      }

      if( $db && password_verify($_POST['password'], $db['user_password']) ){
        $bytes = random_bytes(16);
        $hash = password_hash(bin2hex($bytes), PASSWORD_DEFAULT);
        $this->post_db->updateHash([ 'user_hash' => $hash, 'user_ip' => $this->my_ip, 'user_login' => $_POST['login'] ]);        
        setcookie( 'user_login', $_POST['login'], time() + $this->cookie_time, '/', '.' . $this->domain );
        setcookie( 'hash', $hash, time() + $this->cookie_time, '/', '.' . $this->domain );
        die ( 'done' );
      } else
        die( 'wrong credentials' );
    }


    function check_login()
    { 
      if ( !isset($_COOKIE['user_login']) || empty($_COOKIE['user_login']) || preg_match('/[^\w\-]/iu', $_COOKIE['user_login']) )
        $this->logoff();

      $now = new \DateTime();
      $db = $this->connector->getUser([ $_COOKIE['user_login'] ]);
      $expired_time = strtotime( '+' . $this->cookie_time . ' seconds', strtotime( $db['user_last_logon']) );

      switch (true) {
        case !isset( $_COOKIE['hash'] ):
        case empty( $_COOKIE['hash'] ):
        case $db['user_hash'] !=  $_COOKIE['hash']:
        case $db['user_login'] != $_COOKIE['user_login']:
        case $db['user_ip'] != $this->my_ip:
        case $now->getTimestamp() >= $expired_time:
          $this->logoff();
        default:
          $this->post_db->updateHash([ 'user_last_logon' =>  date('Y-m-d H:i:s'), 'user_login' => $_COOKIE['user_login'] ]);
          setcookie( 'user_login', $db['user_login'], time() + $this->cookie_time, '/', '.' . $this->domain );
          setcookie( 'hash', $db['user_hash'], time() + $this->cookie_time, '/', '.' . $this->domain );
          die( 'all_ok' );
      }
  }

}
