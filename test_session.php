<?php
session_start();

// --- TEST SETTINGS ---
// Change these to see the Navbar change!

/*// 1. To test as a MEMBER:
$_SESSION['user_id'] = 1;
$_SESSION['username'] = "JaneMember";
$_SESSION['role'] = "member";
*/

 // 2. To test as an ADMIN (Uncomment this and comment out the Member section above):
$_SESSION['user_id'] = 2;
$_SESSION['username'] = "AdminBoss";
$_SESSION['role'] = "admin";

/*
 // 3. To test as a GUEST (Uncomment this to "Log Out"):
session_destroy(); 
*/

echo "Session Updated! <a href='index.php'>Go to Home to see the Navbar</a>";
?>