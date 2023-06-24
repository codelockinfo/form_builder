<?php
function zeroise( $number, $threshold ) {
	return sprintf( '%0' . $threshold . 's', $number );
}
function backslashit( $string ) {
	if ( isset( $string[0] ) && $string[0] >= '0' && $string[0] <= '9' )
		$string = '\\\\' . $string;
	return addcslashes( $string, 'A..Za..z' );
}