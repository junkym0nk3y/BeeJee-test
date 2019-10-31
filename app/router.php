<?php
  namespace App;
  class Router
  {
    private $request;
    
    function __construct( $domain ) {
      if ( !empty($_POST) ) {
        define( 'IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' );
        if( !IS_AJAX ) die( 'Restricted access' );
        $to_explode = str_replace( $domain,'',$_SERVER['HTTP_REFERER'] );
      } else
        $to_explode = $_SERVER['REQUEST_URI'];

      $url_request = explode( '/', $to_explode );
      !empty($url_request[1]) ? $this->request = preg_replace( '/[^\w\-]/iu', '', $url_request[1] ) : "first page";
    }

    public function getAction() {
      if ( !empty($_POST) && isset($_POST['admin']) ) // !
        return 'admin_post';
      elseif ( !empty($_POST) && isset($_POST['auth']) )  // done
        return 'auth';
      elseif ( !empty($_POST) && isset($_POST['new_task']) )  // done
        return 'new_task';
      elseif ( !empty($url_request[1]) && $this->request == 'admin' )  // !
        return 'admin_page';
      else
        return 'home';
    }

    public function handler() {
    
    }
  }
