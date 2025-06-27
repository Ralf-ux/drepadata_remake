<?php
session_start();
session_unset();
session_destroy();
// Redirect to the logout redirect page after logout
header("Location: ../client/logoutRedirect.php");
exit();
?>
