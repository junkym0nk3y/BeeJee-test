<?php
  date_default_timezone_set( 'Europe/Moscow' );
  header( 'Content-type: text/html; charset=utf-8' );
  setlocale( LC_TIME, 'ru_RU.utf8' );

  require './vendor/autoload.php';
  use \App\PDOstart as PDOstart;
  use \App\Controllers\GetDB as GetDB;
  use \App\Router as Router;
  $domain = 'https://' . $_SERVER['HTTP_HOST'];
  $router = new Router( $domain );
  //$action =;
  $settings = parse_ini_file( '/var/www/beejee.ini', true );
  PDOstart::getConnect( $settings['database'] );
  $connector = new GetDB();
  $db = $connector->getUserData();

  $loader = new Twig_Loader_Filesystem('./template');
  $twig = new Twig_Environment( $loader, [] );
  echo $twig->render( 'home.twig', [ 'db' => $db,  'title' => 'Тестовое задание' ] );
?>
