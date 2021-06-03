<?php

$cart[] = 13;
$cart[] = "foo";
$cart[] = "obj";



 echo gettype(count($cart));

 $cart = [];


 echo gettype(count($cart));