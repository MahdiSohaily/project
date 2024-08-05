<?php
function log_action($file, $query, $user)
{
    $logFile = "../../logs/$file.txt"; // Define the log file name or path

    // Get the current date and time
    $timestamp = date('Y-m-d H:i:s');

    // Create a log entry
    $logEntry = "[$timestamp] Action: $query, User:" . $user . "\n";

    // Open or create the log file in append mode
    $file = fopen($logFile, 'a');

    // Write the log entry to the file
    if ($file) {
        fwrite($file, $logEntry);
        fclose($file);
    } else {
        // Handle the case where the file couldn't be opened
        // You can log this error to another file or take other actions as needed.
    }
}
