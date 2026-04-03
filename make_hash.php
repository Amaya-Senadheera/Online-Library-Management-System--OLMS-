<?php
// Use your server's exact logic to make the hash
$password = '123';
$hashed = password_hash($password, PASSWORD_DEFAULT);

echo "<h3>Your Server's Verified Hash for '123':</h3>";
echo "<code style='background:#eee; padding:10px; display:block;'>" . $hashed . "</code>";
echo "<br><p>Copy the code above and paste it into your SQL seed file.</p>";
?>