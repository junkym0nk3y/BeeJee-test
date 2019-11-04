<?php
  date_default_timezone_set( 'Europe/Moscow' );
  header( 'Content-type: text/html; charset=utf-8' );
  setlocale( LC_TIME, 'ru_RU.utf8' );
  $settings = parse_ini_file( '/var/www/beejee.ini', true );

  require './vendor/autoload.php';
  use \App\PDO_start as PDO_start;
  use \App\Controllers\Get_DB as Get_DB;
  use \App\Controllers\Post_DB as Post_DB;
  use \App\Controllers\Auth as Auth;
  use \App\Router as Router;
  
  $router = new Router( 'https://' . $_SERVER['HTTP_HOST'] );
  $action = $router->getAction();
  PDO_start::getConnect( $settings['database'] );
  

  $ip = str_replace( '.', '', $_SERVER['REMOTE_ADDR'] );
  if ( $action  == 'home' ) {
    $connector = new Get_DB( 'tasks' );
    $db = $connector->getTaskData();
    $loader = new Twig_Loader_Filesystem( './template' );
    $twig = new Twig_Environment( $loader, [] );
    echo $twig->render( 'home.twig', [ 'db' => $db, 'title' => 'Тестовое задание' ] );
  } elseif ( $action == 'new_task' ){
    $post_db = new Post_DB( 'tasks' );
    $email = $_POST['email'];
    $username = trim( $_POST['username'] );
    $descript = trim( $_POST['descript'] );
    switch (true) {
      case empty( $email ):
        exit( 'Вы не указали свой e-mail' );
      case !preg_match( '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/', $email ):
        exit( 'Неверный формат e-mail адреса' );
      case empty( $username );
        exit( 'Вы не указали своё имя' );
      case empty( $descript );
        exit( 'Вы не рассказали о задаче' );
      default:
        $post_db->addData( [ 'username', 'email', 'descript' ] );
        exit( 'done' );
    }
  } elseif ($action == 'auth') {
    $auth = new Auth( $_SERVER['HTTP_HOST'], $ip, 600 );
    $auth->connect();
  } elseif ($action == 'admin_page' || $action = 'admin_post' ){
    $auth = new Auth( $_SERVER['HTTP_HOST'], $ip, 600 );
    $check_login = $auth->check_login();
    if ($check_login == 'all_ok'){
      exit ('good');
    }

    exit ('bad');
  }

?>
