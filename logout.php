<?php
session_start();
session_destroy();
header('Location: login.php?message=' . urlencode("Vous avez été déconnecté avec succès."));
exit();
?>