<?php

    require_once('../config/connection.php');
    require_once('../helpers/helperFunctions.php');

    if(isset($_REQUEST['command'])) {
        $controller = new BookController();
        if($_REQUEST['command'] == 'addNewBook') {
            $controller->addNewBook();
        }  else if($_REQUEST['command'] == 'deleteBook') {
            if(isset($_GET['book_id'])) {
                $controller->deleteBook($_GET['book_id']);
            }
        } else if($_REQUEST['command'] == 'getBookById') {
            if(isset($_GET['book_id'])) {
                $controller->getBookById($_GET['book_id']);
            }
        } else if($_REQUEST['command'] == 'updateBook') {
            if(isset($_GET['book_id'])) {
                $controller->updateBook($_GET['book_id']);
            }
        } 
    }
    
    class BookController {

        public function __construct()
        {
            $db = new Connection;
            $this->conn = $db->open();

            $this->helper = new helperFunctions;
        }

        public function addNewBook() {
            $title = $_POST['title'];
            $isbn = $_POST['isbn'];
            $author = $_POST['author'];
            $publisher = $_POST['publisher'];
            $year_published = $_POST['year_published'];
            $category = $_POST['category'];

            $query = "INSERT INTO `books`(`title`, `isbn`, `author`, `publisher`, `year_published`, `category`)
                        VALUES ('$title','$isbn','$author','$publisher','$year_published','$category');";
            $this->conn->query($query);
            echo $this->helper->returnData('success','New Book has been added.', '');
        }

        public function deleteBook($book_id) {
            if($book_id) {
                $query = "DELETE FROM `books` WHERE `book_id` = $book_id;";
                $this->conn->query($query);
                echo $this->helper->returnData('success','Book has been deleted.', '');
            } else {
                echo $this->helper->returnData('error','Something went wrong!.', '');
            }
            
        }
        
        public function getBookById($book_id) {
            if ($book_id) {
                $query = "SELECT * FROM books WHERE book_id = :book_id;";
                $res = $this->conn->prepare($query);
                $res->bindParam(':book_id', $book_id, PDO::PARAM_INT); // Use parameterized query
                $res->execute();
    
                $result = $res->fetch(PDO::FETCH_OBJ); // Fetch result as an object
    
                if ($result) {
                    echo $this->helper->returnData('success', 'Book retrieved successfully.', $result);
                } else {
                    echo $this->helper->returnData('error', 'No book found with the given ID.', null);
                }
            } else {
                echo $this->helper->returnData('error', 'Invalid book ID.', null);
            }
        }

        public function updateBook($book_id) {
            if ($book_id) {
                $title = $_POST['title'];
                $isbn = $_POST['isbn'];
                $author = $_POST['author'];
                $publisher = $_POST['publisher'];
                $year_published = $_POST['year_published'];
                $category = $_POST['category'];
                
                $query = "UPDATE books SET title = :title, isbn = :isbn, author = :author, publisher = :publisher, year_published = :year_published, category = :category WHERE book_id = :book_id;";
                $res = $this->conn->prepare($query);
                $res->execute(array(
                    ':title' => $title,
                    ':isbn' => $isbn,
                    ':author' => $author,
                    ':publisher' => $publisher,
                    ':year_published' => $year_published,
                    ':category' => $category,
                    ':book_id' => $book_id,
                ));
                echo $this->helper->returnData('success','Book is now updated.', '');
            } else {
                echo $this->helper->returnData('error', 'Invalid book ID.', null);
            }
            
        }
        
    }

?>