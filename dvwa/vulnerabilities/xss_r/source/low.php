<?php

header( 'X-XSS-Protection: 1; mode=block' );

if( array_key_exists( "name", $_GET ) && $_GET[ 'name' ] != NULL ) {
    // Sanitize input for HTML context
    $name = htmlspecialchars( $_GET[ 'name' ], ENT_QUOTES, 'UTF-8' );

    // Append to DVWA $html variable
    $html .= "<pre>Hello " . $name . "</pre>";
}

?>