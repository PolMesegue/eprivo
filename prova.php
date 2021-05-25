<?php


$tmpfname = tempnam("/var/www", 'orm');
$handle = fopen($tmpfname, "w");
fwrite($handle, "code,reps".PHP_EOL);

fclose($handle);

?>


