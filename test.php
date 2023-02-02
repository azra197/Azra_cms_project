<?php include "includes/header.php"; ?>
<?php include "includes/db.php"; ?>
<?php include "includes/navigation.php"; ?>

<?php 

echo loggedInUserId();

if(userLikedThisPost(1)) {
    echo "user liked it";
} else {
    echo "not";
}


?>