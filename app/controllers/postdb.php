<?php
  namespace App\Controllers;
  use \App\Controllers\Get_DB as Get_DB;

  /**
   * Send data to DB.
   */
  class Post_DB extends Get_DB { 
    function __construct( string $table ) {
      parent::__construct( $table );
    }


    /**
     * Helper for PDO, filter post and converts it to SET.
     * @param  array  $post     Post data
     * @param  array  $allowed  Whitelist
     * @return array            Multidimension array 
     */
    private function postToQuery( array $allowed ): array
    {
      $set = '';
      $values = [];

      foreach ( $allowed as $field ) {
        if ( isset($_POST[$field]) ) {
          $set .= '`'. str_replace('`', '``', $field) . "`=:$field, ";
          $values[$field] = $_POST[$field];
        }
      }

      return [ substr($set, 0, -2), $values ];
    }


    /**
     * Add new task.
     * @param  array  $post     Post data
     * @param  array  $allowed  Whitelist
     * @return array            DB operation result
     */
    public function addData( array $allowed ) {
      list( $set, $values ) = $this->postToQuery( $allowed );
      $query = "INSERT INTO $this->table SET $set";
      parent::request( $query, $values );
    }


    /**
     * Removes task by id.
     * @param  int    $task_id  Task ID
     * @return array            DB operation result
     */
    public function deleteData( int $task_id ) {
      $query = "DELETE FROM $this->table WHERE id = ?";
      $result = parent::request( $query, [ $task_id ] );
      return $result;
    }
  }
  