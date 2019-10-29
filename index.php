<?php
  date_default_timezone_set( 'Europe/Moscow' );
  header( 'Content-type: text/html; charset=utf-8' );
  setlocale( LC_TIME, 'ru_RU.utf8' );

  require './vendor/autoload.php'; // Twig init
  use \App\PDO_start as PDO_start; // PDO init
  
  $settings = parse_ini_file( '/var/www/beejee.ini', true );
  PDO_start::getConnect( $settings['database'] );

  $loader = new Twig_Loader_Filesystem('./template');
  $twig = new Twig_Environment( $loader, [] );
  echo $twig->render( 'home.twig', [] );
?>
