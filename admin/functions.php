<?php


//===== DATABASE HELPER FUNCTIONS =====//
function stmtConnection(){
    global $connection;
    return mysqli_stmt_init($connection);
}

function escape($string)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($string));
}


function redirect($location)
{
    return header("Location:" . $location);
    exit;
}

//function to make a connection to a database
function query($query)
{
    global $connection;
    $result = mysqli_query($connection, $query);
    confirmQuery($result);
    return $result;
}

function fetchRecords($result)
{
    return mysqli_fetch_array($result);
}

function recordCount($table) //from admin/index.php queries returning the count of the record in the database in each table
{
    global $connection;
    $query = "SELECT * FROM " . $table;
    $select_all_post = mysqli_query($connection, $query);
    $result = mysqli_num_rows($select_all_post);
    confirmQuery($result);

    return $result;
}

function count_records($result) //from admin/index.php queries returning the count of the record in the database in each table
{
    return mysqli_num_rows($result);
}
//===== END DATABASE FUNCTIONS =====//

//===== GENERAL HELPER FUNCTIONS =====//

function get_user_name()
{

    return isset($_SESSION['username']) ? $_SESSION['username'] : null;

    /* moze i ovako ili kao ovo gore
    if (isset($_SESSION['username'])) { 
        return $_SESSION['username']; 
     } 
     */
}
//===== END GENERAL HELPER FUNCTIONS =====//


//===== AUTHENTICATION FUNCTIONS =====//

function is_admin()
{
    if (isLoggedIn()) {

        $result = query("SELECT user_role FROM users WHERE user_id = " . $_SESSION['user_id'] . "");

        $row = fetchRecords($result);

        if (isset($row['user_role'])) {
            //echo $row['user_role'];
            $row['user_role'];
        }
        if ($row) {
            if ($row['user_role'] == 'admin') {
                return true;
            } else {
                return false;
            }
        }
    }
    return false;
}

//===== END AUTHENTICATION FUNCTIONS =====//

//===== USER SPECIFIC HELPER FUNCTIONS =====//

function is_the_logged_in_user_owner($post_id){
    $result = query("SELECT user_id FROM posts WHERE post_id={$post_id} AND user_id=".loggedInUserId()."");
    return count_records($result) >= 1 ? true : false;
}

function get_all_user_posts()
{
    return query("SELECT * FROM posts WHERE user_id=" . loggedInUserId() . "");
}

function get_all_post_user_comments()
{
    return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id = comments.comment_post_id
    WHERE user_id=" . loggedInUserId() . "");
}

function get_all_user_categories()
{
    return query("SELECT * FROM categories WHERE user_id=" . loggedInUserId() . "");
}

function get_all_user_published_posts()
{
    return query("SELECT * FROM posts WHERE user_id=" . loggedInUserId() . " AND post_status='published'");
}

function get_all_user_draft_posts()
{
    return query("SELECT * FROM posts WHERE user_id=" . loggedInUserId() . " AND post_status='draft'");
}

function get_all_user_approved_posts_comments()
{
    return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id = comments.comment_post_id
    WHERE user_id=" . loggedInUserId() . " AND comment_status='approved'");
}

function get_all_user_unapproved_posts_comments()
{
    return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id = comments.comment_post_id
    WHERE user_id=" . loggedInUserId() . " AND comment_status='unapproved'");
}

//===== END USER SPECIFIC HELPER FUNCTIONS =====//


function ifItIsMethod($method = null)
{
    if ($_SERVER['REQUEST_METHOD'] == strtoupper($method)) {
        return true;
    }
    return false;
}

function isLoggedIn()
{ // the way you pass a parametar you can always check which session you want
    if (isset($_SESSION['user_role'])) {
        return true;
    }
    return false;
}

function loggedInUserId()
{
    if (isLoggedIn()) {
        $result = query("SELECT * FROM users WHERE username ='" . $_SESSION['username'] . "'");
        confirmQuery($result);
        $user = mysqli_fetch_array($result);
        if (mysqli_num_rows($result) >= 1) {
            return $user['user_id'];
        }
    }
    return false;
}

function userLikedThisPost($post_id)
{
    //$query = "SELECT * FROM likes WHERE user_id=" . loggedInUserId() . " AND post_id={$post_id}";
    //$result = mysqli_query($connection, $query);

    $result = query("SELECT * FROM likes WHERE user_id=" . loggedInUserId() . " AND post_id = {$post_id}");
    confirmQuery($result);
    return mysqli_num_rows($result) >= 1 ? true : false; // if it's > or = than 1, return true, else false

}

//checking if the user is logged in and redirect user
function checkIfUserIsLoggedInAndRedirect($redirectLocation = null)
{
    if (isLoggedIn()) {
        redirect($redirectLocation);
    }
}

function getPostLikes($post_id)
{
    $result = query("SELECT * FROM likes WHERE post_id=$post_id");
    confirmQuery($result);
    echo mysqli_num_rows($result);
}

function users_online()
{

    if (isset($_GET['onlineusers'])) {

        global $connection;

        if (!$connection) {

            session_start();
            include("../includes/db.php");

            $session = session_id();
            $time = time();
            $time_out_in_seconds = 05;
            $time_out = $time - $time_out_in_seconds;

            $query = "SELECT * FROM users_online WHERE session = '$session'";
            $send_query = mysqli_query($connection, $query);
            $count = mysqli_num_rows($send_query);

            if ($count == NULL) {
                mysqli_query($connection, "INSERT INTO users_online(session,time) VALUES('$session','$time')");
            } else {
                mysqli_query($connection, "UPDATE users_online SET time = '$time' WHERE session = '$session'");
            }

            $users_online_query = mysqli_query($connection, "SELECT * FROM users_online WHERE time > '$time_out'");
            echo $count_user = mysqli_num_rows($users_online_query);
        } // get request isset()

    }
}
users_online();

function confirmQuery($result)
{

    global $connection;

    if (!$result) {
        die("QUERY FAILED ." . mysqli_error($connection));
    }
}

function imagePlaceHolder($image = '')
{
    if (!$image) {
        return 'image_1.jpg';
    } else {
        return $image;
    }
}

function currentUser()
{
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    } else {
        return false;
    }
}

function insert_categories()
{

    global $connection;

    if (isset($_POST['submit'])) {
        $cat_title = $_POST['cat_title'];

        if ($cat_title == "" || empty($cat_title)) {
            echo "This field should not be empty";
        } else {
            $query = "INSERT INTO categories(cat_title)";
            $query .= "VALUE('{$cat_title}') ";

            $create_category_query = mysqli_query($connection, $query);

            if (!$create_category_query) {
                die('QUERY FAILED' . mysqli_error($connection));
            }
        }
    }
}


function findAllCategories()
{

    global $connection;

    $query = "SELECT * FROM categories";
    $select_categories = mysqli_query($connection, $query);

    while ($row = mysqli_fetch_assoc($select_categories)) {
        $cat_id = $row['cat_id'];
        $cat_title = $row['cat_title'];

        echo "<tr>";

        echo "<td>{$cat_id}</td>";
        echo "<td>{$cat_title}</td>";
        echo "<td><a class='btn btn-danger' href='categories.php?delete={$cat_id}'>Delete</a></td>";
        echo "<td><a class='btn btn-info' href='categories.php?edit={$cat_id}'>Edit</a></td>";
        echo "</tr>";
    }
}


function delete_categories()
{

    global $connection;

    if (isset($_GET['delete'])) {
        $the_cat_id = $_GET['delete'];

        $query = "DELETE FROM categories WHERE cat_id = {$the_cat_id} ";
        $delete_query = mysqli_query($connection, $query);
        header("Location: categories.php");
    }
}

function checkStatus($table, $column, $status)
{ //from admin/index.php queries to check status from tables

    global $connection;

    $query = "SELECT * FROM $table WHERE $column = '$status' ";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    return mysqli_num_rows($result);
}

function checkUserRole($table, $column, $role)
{

    global $connection;
    $query = "SELECT * FROM $table WHERE $column = '$role' ";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    return mysqli_num_rows($result);
}



function username_exists($username)
{

    global $connection;

    $query = "SELECT username FROM users WHERE username = '$username'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    if (mysqli_fetch_array($result) > 0) {
        return true;
    } else {
        return false;
    }
}

function email_exists($email)
{

    global $connection;

    $query = "SELECT user_email FROM users WHERE user_email = '$email'";
    $result = mysqli_query($connection, $query);
    confirmQuery($result);

    if (mysqli_fetch_array($result) > 0) {
        return true;
    } else {
        return false;
    }
} 

function register_user($username, $email, $password)
{
    global $connection;

    $username = mysqli_real_escape_string($connection, $username);
    $email = mysqli_real_escape_string($connection, $email);
    $password = mysqli_real_escape_string($connection, $password);

    $password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

    //$query = "SELECT randSalt FROM users";
    //$select_randsalt_query = mysqli_query($connection, $query);
    //$row = mysqli_fetch_array($select_randsalt_query);
    // $salt = $row['randSalt'];
    //$password = crypt($password, $salt); -ne radi ovako preko randSalt
    // $hashFormat = "$2y$10$";
    //$salt = "iusesomecrazystrings22";
    // $hashF_and_salt = $hashFormat . $salt;
    // $password = crypt($password, $hashF_and_salt);

    $query = "INSERT INTO users(username, user_email, user_password, user_role) ";
    $query .= "VALUES('{$username}', '{$email}', '{$password}', 'subscriber' )";
    $register_user_query = mysqli_query($connection, $query);

    confirmQuery($register_user_query);

    //$message = "Your Registration has been submitted.";
}


function login_user($username, $password)
{
    global $connection;

    $username = trim($username);
    $password = trim($password);

    $username = mysqli_real_escape_string($connection, $username);
    $password = mysqli_real_escape_string($connection, $password);

    $query = "SELECT * FROM users WHERE username = '{$username}' ";
    $select_user_query = mysqli_query($connection, $query);

    if (!$select_user_query) {
        die("QUERY FAILED" . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_assoc($select_user_query)) {

        $db_user_id = $row['user_id'];
        $db_username = $row['username'];
        $db_user_password = $row['user_password'];
        $db_user_firstname = $row['user_firstname'];
        $db_user_lastname = $row['user_lastname'];
        $db_user_role = $row['user_role'];

        if (password_verify($password, $db_user_password)) {

            $_SESSION['user_id'] = $db_user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['firstname'] = $db_user_firstname;
            $_SESSION['lastname'] = $db_user_lastname;
            $_SESSION['user_role'] = $db_user_role;

            redirect("/cms/admin");
        } else {
            return false;
        }
    }
    return true;
}
