<?php

    require_once('app/config/connection.php');

    class OnLoadController {

        public function __construct()
        {
            $db = new Connection;
            $this->conn = $db->open();
        }
    
        public function getBooks() {
            $query = 'SELECT * FROM books';
            $res = $this->conn->prepare($query);
            $res->execute();
            return $res;
        }
    
     }

?>