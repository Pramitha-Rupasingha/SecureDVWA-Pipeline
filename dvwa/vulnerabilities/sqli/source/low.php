<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
    // Get input
    $id = $_REQUEST[ 'id' ];

    // Check database connection and sanitize input
    if (isset($GLOBALS["___mysqli_ston"])) {
        $id = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $id);
    }

    $query  = "SELECT first_name, last_name FROM users WHERE user_id = '$id';";
    $result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );

    if ($result && mysqli_num_rows($result) > 0) {
        while( $row = mysqli_fetch_assoc( $result ) ) {
            $first = $row["first_name"];
            $last  = $row["last_name"];

            $html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
        }
    } else {
        $html .= "<pre>No results found.</pre>";
    }
}

?>