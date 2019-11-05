<?php
  namespace App\Controllers;
  use \App\Controllers\Get_DB as Get_DB;
  use \App\Controllers\Post_DB as Post_DB;
  

  class Auth
  {
    private $domain, $my_ip, $cookie_time, $get_db, $post_db;    


    /**
     * [__construct description]
     * @param string  $domain      [description]
     * @param int     $my_ip       [description]
     * @param int     $cookie_time [description]
     * @param Get_DB  $get_db      [description]
     * @param Post_DB $post_db     [description]
     */
    function __construct( string $domain, int $my_ip, int $cookie_time, Get_DB $get_db, Post_DB $post_db )
    {
      $this->domain = $domain;
      $this->my_ip = $my_ip;
      $this->cookie_time = $cookie_time;
      $this->connector = $get_db;
      $this->post_db = $post_db;
    }


    /**
     * [logoff description]
     * @return [type] [description]
     */
    private function logoff(): void
    {
      setcookie( 'user_login', '', time() -1, '/', '.' . $this->domain );
      setcookie( 'hash', '', time() -1, '/', '.' . $this->domain );
      header( 'Location: /' );
      die( 'fuckoff' );
    }


    /**
     * [connect description]
     * @return [type] [description]
     */
    public function connect(): void
    {
      switch (true) {
        case !isset( $_POST['login'] ):
        case !isset( $_POST['password'] ):
        case empty( $_POST['login'] ):
        case empty( $_POST['password' ]):
        case preg_match( '/[^\w\-]/iu', $_POST['login'] ):
          die( 'Данные введены неправильно' );
        default:
          $db = $this->connector->getUser([ $_POST['login'] ]);
          $this->post_db->updateHash([ 'user_hash' => '', 'user_login' => $_POST['login'] ]);
          break;
      }

      if ( $db && password_verify($_POST['password'], $db['user_password']) ){
        $bytes = random_bytes(16);
        $hash = password_hash(bin2hex($bytes), PASSWORD_DEFAULT);
        $this->post_db->updateHash([ 'user_hash' => $hash, 'user_ip' => $this->my_ip, 'user_login' => $_POST['login'] ]);        
        setcookie( 'user_login', $_POST['login'], time() + $this->cookie_time, '/', '.' . $this->domain );
        setcookie( 'hash', $hash, time() + $this->cookie_time, '/', '.' . $this->domain );
        die ( 'done' );
      } else
        die( 'Неправильный логин или пароль' );
    }


    /**
     * [check_login description]
     * @return [type] [description]
     */
    public function check_login(): string
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
          return( 'all_ok' );
      }
  }

}
