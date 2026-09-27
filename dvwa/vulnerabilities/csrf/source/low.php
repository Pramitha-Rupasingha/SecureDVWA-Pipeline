<?php

if( isset( $_GET[ 'Change' ] ) ) {
    // Check anti-CSRF token
    checkToken( $_REQUEST[ 'user_token' ],$_SESSION[ 'session_token' ], 'index.php' );

    // Get input
    $pass_new  =$_GET[ 'password_new' ];
    $pass_conf =$_GET[ 'password_conf' ];

    // Do the passwords match?
    if( $pass_new == $pass_conf ) {$pass_new = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $pass_new);
        $pass_new = md5($pass_new );

        // Update database
        $insert = "UPDATE `users` SET password = '$pass_new' WHERE user = '" . dvwaCurrentUser() . "';";
        $result = mysqli_query($GLOBALS["___mysqli_ston"],  $insert );

        $html .= "<pre>Password Changed.</pre>";
    } else {
        $html .= "<pre>Passwords did not match.</pre>";
    }
}

// Generate anti-CSRF token
generateSessionToken();

?>