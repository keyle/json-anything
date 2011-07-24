<pre>



<?php
//header('Cache-Control: no-cache, must-revalidate');
//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
//header('Content-type: application/json');

function filterNasty($stuff) {
	return filter_var(
			htmlspecialchars(
				pg_escape_string($stuff)
			),
			FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH
		);
}

require_once('phpQuery-onefile.php');

if(!$_GET['url']) die('give a url param.');

$_GET['url'] = filterNasty($_GET['url']);
$url = $_GET['url'];

if(!$_GET['s1']) die("give at least 1 selector, ie. ...?s1=.class a");

$_GET['s1'] = filterNasty($_GET['s1']);
$s1 = $_GET['s1'];

$selector1 = explode('|', $s1);

if(count($selector1) < 2)
	$selector1[1] = null;

$output = file_get_contents('http://'.$url) or die('Could not access: ?url=...');

phpQuery::newDocument($output);

//echo pq('title')->html() . "\n";
//echo pq('body')->find('img');

foreach($_GET as $name => $value)
{
	print "$name : $value<br>";
}

var_dump($selector1);

//$j = json_encode(filterThis($selector1[0], $selector1[1]));
//echo $j;
var_dump(filterThis($selector1[0], $selector1[1]));

function filterThis($selector, $attribute = null)
{
	$arr = array();

	foreach(pq($selector) as $item)
	{
		$attribute == null ?
		array_push($arr, pq($item)->html()) :
		array_push($arr, pq($item)->attr($attribute));
	}

	return $arr;
}

?>


