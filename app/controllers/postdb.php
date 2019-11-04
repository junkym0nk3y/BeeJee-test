<?php
  namespace App\Controllers;
  use \App\Controllers\Get_DB as Get_DB;


  class Post_DB extends Get_DB { 
    function __construct( string $table ) {
      parent::__construct( $table );
    }


    private function postToQuery( array $post, array $allowed ): array
    {
      $set = '';
      $values = [];

      foreach ( $allowed as $field ) {
        if ( isset($post[$field]) ) {
          $set .= '`'. str_replace('`', '``', $field) . "`=:$field, ";
          $values[$field] = $post[$field];
        }
      }

      return [ substr($set, 0, -2), $values ];
    }


    public function addData( array $allowed ) {
      list( $set, $values ) = $this->postToQuery( $_POST, $allowed );
      $query = "INSERT INTO $this->table SET $set";
      parent::request( $query, $values );
    }


    public function updateHash( array $post ) {
      list( $set, $values ) = $this->postToQuery( $post, ['user_hash', 'user_ip', 'user_last_logon'] );
      $values['user_login'] = $post['user_login'];
      $query = "UPDATE users SET $set WHERE user_login = :user_login";
      parent::request( $query, $values );
    }


    public function deleteData( int $task_id ) {
      $query = "DELETE FROM $this->table WHERE id = ?";
      $result = parent::request( $query, [ $task_id ] );
      return $result;
    }
  }
  