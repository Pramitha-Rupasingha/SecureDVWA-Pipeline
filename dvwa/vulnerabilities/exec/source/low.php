<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
    // Get input
    $target = $_REQUEST[ 'ip' ];

    // Validate IP address format
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Determine OS and execute ping command
        if( stristr( php_uname( 's' ), 'Windows' ) ) {
            $cmd = shell_exec( 'ping ' . $target );
        } else {
            $cmd = shell_exec( 'ping -c 4 ' . $target );
        }
        $html .= "<pre>{$cmd}</pre>";
    } else {
        $html .= "<pre>ERROR: You have entered an invalid IP address.</pre>";
    }
}

?>