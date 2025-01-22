<?php
    include_once "app/controller/OnLoadController.php";
    $OnLoadController = new OnLoadController();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Catalog</title>
    <?php @include 'assets/includes/style.php'; ?>
</head>

<body class="body">

    <div class="book-parent container">
        <div class="d-flex justify-content-end ">
            <button type="submit" 
                    class="btn btn-success font-weight-bold pr-5 pl-5" 
                    data-toggle="modal" 
                    data-target="#addBookModal">
                    Add
            </button>

        </div>
        <table class="table table-bordered bg-white ">
            <thead class="text-center">
                <tr>
                    <th scope="col">TITLE</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">AUTHOR</th>
                    <th scope="col">PUBLISHER</th>
                    <th scope="col">YEAR PUBLISHED</th>
                    <th scope="col">CATEGORY</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody class="text-muted">
            <?php 
                $getBooks = $OnLoadController->getBooks();
                if ($getBooks->rowCount() > 0) {
                    while ($data = $getBooks->fetch()) { 
            ?>
                <tr>
                    <td><?php echo $data['title'] ?></td>
                    <td><?php echo $data['isbn'] ?></td>
                    <td><?php echo $data['author'] ?></td>
                    <td><?php echo $data['publisher'] ?></td>
                    <td><?php echo $data['year_published'] ?></td>
                    <td><?php echo $data['category'] ?></td>
                    <td>
                        <button type="submit" 
                                class="btn btn-secondary btn-sm font-weight-bold edit_book" 
                                data-id="<?php echo $data['book_id'] ?>">
                                EDIT
                        </button>
                        <button type="submit" 
                            class="btn btn-secondary btn-sm font-weight-bold remove_book" 
                            data-id="<?php echo $data['book_id'] ?>">
                            DEL
                        </button>
                    </td>
                </tr>
            <?php  
                }
                } else {
                    echo '<tr><td colspan="7" class="text-center">No books found</td><tr>';
                }
            
            ?>
            </tbody>
        </table>
    </div>


     <!-- add book modal -->
     <div class="modal fade" id="addBookModal" tabindex="-1" role="dialog" aria-labelledby="modal_title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Add Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-xmark h3"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="submit-new-book" method="POST">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="title">ISBN</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required>
                        </div>
                        <div class="form-group">
                            <label for="title">AUTHOR</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="title">PUBLISHER</label>
                            <input type="text" class="form-control" id="publisher" name="publisher" required>
                        </div>
                        <div class="form-group">
                            <label for="title">YEAR PUBLISHED</label>
                            <input type="number" class="form-control" id="year_published" name="year_published" min="1800" max="2025" required>
                        </div>
                        <div class="form-group">
                            <label for="title">CATEGORY</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" id="submit_btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php @include 'assets/includes/script.php'; ?>
<script>
    $(document).ready(function() {
        var url = 'app/controller/BookController.php';
        var book_update_id = 0;
        //adding new book
        $("#submit-new-book").submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            let path;
            if(book_update_id > 0) {
                path = url + `?command=updateBook&book_id=${book_update_id}`;
            } else {
                path = url + `?command=addNewBook`;
            }
            $.SystemScript.executePost(path, data).done((response) => {
                console.log(response.data);
                if (response.data.status == 'success') {
                    $.SystemScript.swalAlertMessage('Successfully', `${response.data.message}`, 'success');
                    $('.swal2-confirm').click(function(){
                        location.reload();
                    });
                } else {
                    $.SystemScript.swalAlertMessage('Error', `${response.data.message}`, 'error');
                }
            });
        });

        //removing books
        $(".remove_book").click(function(e) {
            var book_id = $(this).attr("data-id");
            $.SystemScript.swalConfirmMessage('Are you sure', 
            'Do you want to remove this book?', 'question').done(function(response) {
                if(response) {
                    let path = url + `?command=deleteBook&book_id=${book_id}`;
                    $.SystemScript.executeGet(path).done((response) => {
                        console.log(response.data);
                        if (response.data.status == 'success') {
                            $.SystemScript.swalAlertMessage('Successfully', `${response.data.message}`, 'success');
                            $('.swal2-confirm').click(function(){
                                location.reload();
                            });
                        } else {
                            $.SystemScript.swalAlertMessage('Error', `${response.data.message}`, 'error');
                        }
                    });
                }
            });
        });

        //retreiving data and fetching to modal
        $(".edit_book").click(function(e) {
            var book_id = $(this).attr("data-id");
            let path = url + `?command=getBookById&book_id=${book_id}`;
            $.SystemScript.executeGet(path).done((response) => {
                console.log(response.data);
                if (response.data.status =='success') {
                    let bookData = response.data.data;
                    $("#title").val(bookData.title);
                    $("#isbn").val(bookData.isbn);
                    $("#author").val(bookData.author);
                    $("#publisher").val(bookData.publisher);
                    $("#year_published").val(bookData.year_published);
                    $("#category").val(bookData.category);
                    $("#modal_title").text("Update Book");
                    $("#submit_btn").text("Update");
                    book_update_id = book_id;
                    $("#addBookModal").modal('show');
                } else {
                    $.SystemScript.swalAlertMessage('Error', `${response.data.message}`, 'error');
                }
            });
        });

     
        
    })
</script>
</html>
