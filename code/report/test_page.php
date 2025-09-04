<?php
// Simple test page to verify PHP is working
echo "<!DOCTYPE html>";
echo "<html><head><title>Test Page</title></head>";
echo "<body>";
echo "<h1>PHP Test Page</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p><a href='editLocationFolder.php'>Go to Edit Location Folder</a></p>";
echo "</body></html>";
?>
