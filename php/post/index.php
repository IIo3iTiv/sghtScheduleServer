<?php

if (isset($_POST['a'])) $string_a = htmlentities($_POST['a']);
if (isset($_POST['b'])) $string_b = htmlentities($_POST['b']);
 
$isEmpty = false;
if ((empty($string_a)) || (empty($string_b))) $isEmpty = true;
 
if (!$isEmpty) {
	$a = (int)$string_a;
	$b = (int)$string_b;
 
	$c = $a + $b;
 
	echo '<style type="text/css" media="all">
		  	html, body, div {
				font-size: 96pt;
				color: white;
				background: #0a0a0a;
				display: flex;
				width: 100%;
				height: 100%;
				flex-wrap: wrap;
  				flex-direction: row;
  				justify-content: center;
				align-items: center;  
				overflow:  hidden;
			}
		</style>';
	echo '<div>';
	echo $c;
	echo '</div>';
}
else {
	echo '<style type="text/css" media="all">
		  	html, body, div {
				font-size: 96pt;
				color: white;
				background: #0a0a0a;
				display: flex;
				width: 100%;
				height: 100%;
				flex-wrap: wrap;
  				flex-direction: row;
  				justify-content: center;
				align-items: center;  
				overflow:  hidden;
			}
		</style>';
	echo '<div>';
	echo "error";
	echo '</div>';
}
?>