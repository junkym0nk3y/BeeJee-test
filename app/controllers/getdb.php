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

    function __construct() {
      $this->taskExist();
    }

    /**
     * Checks if table exist and create if does not.
     */
    protected function taskExist(): void
    {
      $result = $this->request( 'SELECT 1 FROM tasks LIMIT 1' );

      if ($result)
        return;
  
      $query = 'CREATE TABLE tasks (
        id               INT(15) NOT NULL AUTO_INCREMENT,
        username         VARCHAR(30) NOT NULL,
        email            VARCHAR(30) NOT NULL,
        descript         VARCHAR(512) NOT NULL,
        status           TINYINT(1),
        edited           TINYINT(1),
        last_edit        TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
      )';
      $this->request( $query );
    }


    /**
     * [getTaskData description]
     * @return [type] [description]
     */
    public function getTaskData(): array
    {
      $query = 'SELECT id, username, email, descript, IF(edited = 1, "Администратором", "") AS edited, 
        IF(status = 1, "Выполнено", "") AS status FROM tasks ORDER BY last_edit DESC';
      $stmt = $this->request( $query );

      return $stmt->fetchAll( PDO::FETCH_ASSOC );
    }


    /**
     * [getTaskDescript description]
     * @param  array  $id [description]
     * @return [type]     [description]
     */
    public function getTaskDescript( array $id )
    {
      $query = 'SELECT descript FROM tasks WHERE id = ? LIMIT 1';
      $stmt = $this->request( $query, $id );

      return $stmt->fetchColumn();
    }


    /**
     * [getUser description]
     * @param  array  $user_login [description]
     * @return [type]             [description]
     */
    public function getUser( array $user_login )
    {
      $query = 'SELECT * FROM users WHERE user_login = ? LIMIT 1';
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
  