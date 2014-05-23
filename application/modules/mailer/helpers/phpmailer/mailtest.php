<?php

/**
 * Edit the variables below
 */

$from = 'somebody@somewhere.com';

$to = 'somebody@somewhere.com';

/**
 * Do not edit any more variables below this line
 */

$subject = 'Test';

$message = 'This is just a test.';

$headers = 'From: ' . $from . "\r\n" .
	'Reply-To: ' . $from . "\r\n" .
	'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message);

?>