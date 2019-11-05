<?php
  namespace App\Controllers;
  use \App\Controllers\Get_DB as Get_DB;


  class Post_DB extends Get_DB { 
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


    /**
     * [addData description]
     * @param array $allowed [description]
     */
    public function addData( array $allowed ): void 
    {
      list( $set, $values ) = $this->postToQuery( $_POST, $allowed );
      $query = "INSERT INTO tasks SET $set";
      parent::request( $query, $values );
    }


    /**
     * [updateData description]
     * @param  array  $allowed [description]
     * @return [type]          [description]
     */
    public function updateData( array $allowed ): void 
    {
      list( $set, $values ) = $this->postToQuery( $_POST, $allowed );
      $values['id'] = $_POST['id'];
      $query = "UPDATE tasks SET $set WHERE id = :id";
      parent::request( $query, $values );
    }


    /**
     * [updateHash description]
     * @param  array  $post [description]
     * @return [type]       [description]
     */
    public function updateHash( array $post ): void
    {
      list( $set, $values ) = $this->postToQuery( $post, ['user_hash', 'user_ip', 'user_last_logon'] );
      $values['user_login'] = $post['user_login'];
      $query = "UPDATE users SET $set WHERE user_login = :user_login";
      parent::request( $query, $values );
    }

  }
  