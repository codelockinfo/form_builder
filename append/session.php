<?php
if (isset($_SESSION['login_user_id']) && $_SESSION['login_user_id'] > 0) {
    $current_user = (array) $_SESSION['current_user'];
 } else {
//    header('Location: index.php') ;
//    exit;
}
?>
