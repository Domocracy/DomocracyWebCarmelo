<?php

$recip    = sanitize(($_GET['recip']) ?$_GET['recip'] : $_POST['recip']); 
$sender    = sanitize(($_GET['sender']) ?$_GET['sender'] : $_POST['sender']); 
$subject  = sanitize(($_GET['subject']) ?$_GET['subject'] : $_POST['subject']); 
$subject .= " sent via Jorp Mail"; 
$message  = sanitize(($_GET['message']) ?$_GET['message'] : $_POST['message']);  
     
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: Jorp Mail <'.$sender.'>' . "\r\n";

mail($recip, $subject, $message, $headers);

?>
