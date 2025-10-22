<?php
session_start();
session_destroy();

// Clear local storage via JavaScript
?>
<!DOCTYPE html>
<html>
<head>
    <script>
        localStorage.clear();
        window.location.href = 'index.php';
    </script>
</head>
</html>