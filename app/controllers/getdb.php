<?php
  namespace App\Controllers;
  use \PDO as PDO;
  use \App\PDO_start as PDO_start;

  /**
   * Get data from DB.
   */
  class Get_DB {
    protected $table;
    protected $sticker_sets;

    function __construct( string $table ) {
      $this->table = $table;
      $this->taskExist();
    }

    /**
     * Checks if table exist and create if does not.
     */
    protected function taskExist(): void
    {
      $result = $this->request( "SELECT 1 FROM tasks LIMIT 1" );

      if ($result)
        return;
  
      $query = "CREATE TABLE tasks (
        id               INT(15) NOT NULL AUTO_INCREMENT,
        username         VARCHAR(30) NOT NULL,
        email            VARCHAR(30) NOT NULL,
        descript         VARCHAR(512) NOT NULL,
        status           TINYINT(1),
        edited           TINYINT(1),
        last_edit        TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
      )";
      $this->request( $query );
    }

    /**
     * Returns all user data
     * @param  int    $user_id
     * @return array
     */
    public function getTaskData(): array
    {
      $query = "SELECT * FROM $this->table ORDER BY last_edit DESC";
      $stmt = $this->request( $query );

      return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    /**
     * Returns all user data
     * @param  int    $user_id
     * @return array
     */
    public function getUser( array $user_login )
    {
      $query = "SELECT * FROM $this->table WHERE user_login = ? LIMIT 1";
      $stmt = $this->request( $query, $user_login );

      return $stmt->fetch( PDO::FETCH_ASSOC );
    }



    /**
     * Send query to database and get or set data.
     * @param  string  $query   MySQL query
     * @param  array   $values  Values for placeholders
     * @return object           PDO Statement 
     */
    protected function request( string $query, array $values = [] ) {
      $stmt = PDO_start::getConnect()->dbh->prepare( $query );

      try {
          $result = $values ? $stmt->execute( $values ) : $stmt->execute();
      } catch (Exception $e) {
          $result = FALSE;
      }
      
      return !$result ? FALSE : $stmt;
    }
    
  }
  