<?php
  namespace App;
  use \App\Controllers\Get_DB as Get_DB;
  use \App\Controllers\Post_DB as Post_DB;
  use \App\Controllers\Auth as Auth;

  class Handler{
    private $ip, $cookie_time, $get_db, $post_db, $twig;
    private $actionMap = [
      'home' => 'mainPage',
      'new_task' => 'postTask',
      'auth' => 'userAuth',
      'admin_page' => 'adminsPage',
      'admin_post' => 'editTask',
    ];
  

    /**
     * [__construct description]
     * @param string $action      [description]
     * @param int    $ip          [description]
     * @param int    $cookie_time [description]
     */
    function __construct( string $action, int $ip, int $cookie_time ) {
      $this->ip = $ip;
      $this->cookie_time = $cookie_time;
  
      $this->get_db = new Get_DB();
      $this->post_db = new Post_DB();
      $loader = new \Twig_Loader_Filesystem( './template' );
      $this->twig = new \Twig_Environment( $loader, [] );
  
      $action = $this->actionMap[$action];
      $this->$action();
    }
  

    /**
     * [mainPage description]
     * @return [type] [description]
     */
    private function mainPage(): void
    {
      $db = $this->get_db->getTaskData();
      echo $this->twig->render( '/ext/home.twig', ['db' => $db, 'title' => 'Тестовое задание'] );
    }
  

    /**
     * [postTask description]
     * @return [type] [description]
     */
    private function postTask(): void
    {
      $email = $_POST['email'];
      $username = trim( $_POST['username'] );
      $descript = trim( $_POST['descript'] );
      switch (true) {
        case preg_match( '/[^\w\-\s]/iu', $username ):
          exit( 'Неверный формат имени пользователя' );
        case empty( $email ):
          exit( 'Вы не указали свой e-mail' );
        case !preg_match( '/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/', $email ):
          exit( 'Неверный формат e-mail адреса' );
        case empty( $username );
          exit( 'Вы не указали своё имя' );
        case empty( $descript );
          exit( 'Вы не рассказали о задаче' );
        default:
          $_POST['descript'] = preg_replace('/<[^>]*>/', '', $descript);
          $this->post_db->addData([ 'username', 'email', 'descript' ]);
          exit( 'done' );
      }
    }


    /**
     * [userAuth description]
     * @return [type] [description]
     */
    private function userAuth(): void
    {
      $auth = new Auth( $_SERVER['HTTP_HOST'], $this->ip, $this->cookie_time, $this->get_db, $this->post_db );
      $auth->connect();
    }
  
   
    /**
     * [adminsPage description]
     * @return [type] [description]
     */
    private function adminsPage()
    {
      $auth = new Auth( $_SERVER['HTTP_HOST'], $this->ip, $this->cookie_time, $this->get_db, $this->post_db );
      $check_login = $auth->check_login();
  
      if ($check_login == 'all_ok'){
        $db = $this->get_db->getTaskData();
        $twig_vars = [ 
          'db' => $db,
          'title' => 'Тестовое задание',
          'domain' => $_SERVER['HTTP_HOST'],
          'cookie_time' => $this->cookie_time,
        ];
        echo $this->twig->render( '/ext/admin.twig', $twig_vars );
      }
    }
  
  
    /**
     * [editTask description]
     * @return [type] [description]
     */
    private function editTask()
    {
      $auth = new Auth( $_SERVER['HTTP_HOST'], $this->ip, $this->cookie_time, $this->get_db, $this->post_db );
      $check_login = $auth->check_login();
  
      $descript = trim( $_POST['descript'] );
      if ( $check_login == 'all_ok' && !empty($descript) ) {
        $old_descript = $this->get_db->getTaskDescript([ $_POST['id'] ]);
  
        if ( $descript != $old_descript)
          $_POST['edited'] = 1;
  
        $_POST['descript'] = preg_replace('/<[^>]*>/', '', $descript);
        $this->post_db->updateData([ 'descript', 'status', 'edited' ]);
        exit( 'updated' );
      } else 
        exit( 'Попробуйте ещё' );
    }

}
