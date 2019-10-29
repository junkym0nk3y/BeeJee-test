<?php
  namespace App\Controllers;
  use \PDO as PDO;
  use \App\PDOstart as PDOstart;

  /**
   * Get data from DB.
   */
  class GetDB {
    function __construct() {
       $this->tableExists();
    }

    /**
     * Checks if table exist and create if does not.
     */
    protected function tableExists(): void
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
    public function getUserData(): array
    {
      $query = "SELECT * FROM tasks";
      $stmt = $this->request( $query );

      return $stmt->fetch( PDO::FETCH_ASSOC );
    }

    /**
     * Send query to database and get or set data.
     * @param  string  $query   MySQL query
     * @param  array   $values  Values for placeholders
     * @return object           PDO Statement 
     */
    protected function request( string $query, array $values = [] ) {
      $stmt = PDOstart::getConnect()->dbh->prepare( $query );

      try {
          $result = $values ? $stmt->execute( $values ) : $stmt->execute();
      } catch (Exception $e) {
          $result = FALSE;
      }

      return !$result ? FALSE : $stmt;
    }
    
  }
  