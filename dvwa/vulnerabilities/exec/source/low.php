<?php
 
if( isset( $_POST[ 'Submit' ]  ) ) {
    // Get input
    $target = $_REQUEST[ 'ip' ];
 
    // Validate IP address format
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Determine OS and execute ping command
        // escapeshellarg() added: extra layer of protection so the validated
        // input can never be interpreted as extra shell arguments/metacharacters.
        if( stristr( php_uname( 's' ), 'Windows' ) ) {
            $cmd = shell_exec( 'ping ' . escapeshellarg( $target ) );
        } else {
            $cmd = shell_exec( 'ping -c 4 ' . escapeshellarg( $target ) );
        }
        $html .= "<pre>{$cmd}</pre>";
    } else {
        $html .= "<pre>ERROR: You have entered an invalid IP address.</pre>";
    }
}
 
?>