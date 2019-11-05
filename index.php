<?php
  date_default_timezone_set( 'Europe/Moscow' );
  header( 'Content-type: text/html; charset=utf-8' );
  setlocale( LC_TIME, 'ru_RU.utf8' );
  $settings = parse_ini_file( '/var/www/beejee.ini', true );

  require './vendor/autoload.php';
  use \App\PDO_start as PDO_start;
  use \App\Router as Router;
  use \App\Handler as Handler;

  $ip = str_replace( '.', '', $_SERVER['REMOTE_ADDR'] );
  PDO_start::getConnect( $settings['database'] );
  $router = new Router( 'https://' . $_SERVER['HTTP_HOST'] );

  new Handler( $router->getAction(), $ip, 600 );
?>
