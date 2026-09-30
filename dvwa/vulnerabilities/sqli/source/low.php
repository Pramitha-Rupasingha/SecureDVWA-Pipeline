<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
    // Get input
    $id = $_REQUEST[ 'id' ];

    // Use a prepared statement instead of a manually-built query string.
    // The user input is bound as a parameter, so it can never change the
    // structure of the SQL query (this is the fix Semgrep asked for:
    // rule php.lang.security.injection.tainted-sql-string.tainted-sql-string).
    if (isset($GLOBALS["___mysqli_ston"])) {
        $stmt = mysqli_prepare(
            $GLOBALS["___mysqli_ston"],
            "SELECT first_name, last_name FROM users WHERE user_id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) > 0) {
                while( $row = mysqli_fetch_assoc( $result ) ) {
                    $first = $row["first_name"];
                    $last  = $row["last_name"];

                    $html .= "<pre>ID: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "<br />First name: {$first}<br />Surname: {$last}</pre>";
                }
            } else {
                $html .= "<pre>No results found.</pre>";
            }

            mysqli_stmt_close($stmt);
        } else {
            $html .= "<pre>No results found.</pre>";
        }
    }
}

?>