<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

function filterNasty($stuff) {
	return filter_var
	(
		htmlspecialchars
		(
			pg_escape_string
			(
				strip_tags($stuff)
			)
		), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH
	);
}

ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; U; ru; rv:5.0.1.6) Gecko/20110501 Firefox/5.0.1 Firefox/5.0.1');

require_once('phpQuery-onefile.php');

if(!$_GET['url']) die('give a url param.');

$_GET['url'] = filterNasty($_GET['url']);
$url = $_GET['url'];

if(!$_GET['sel']) die("give at least 1 selector, ie. ...?sel=.class__a");

$_GET['sel'] = filterNasty($_GET['sel']);
$sel = $_GET['sel'];

$debug = isset($_GET['debug']) ? true : false;

$sel = replaceThisByThatInThat('__', ' ', $sel);
$sel = replaceThisByThatInThat('%', '#', $sel);

$selectorsArr = explode(',', $sel);

for ($i=0; $i < count($selectorsArr); $i++)
{
	$selectorsArr[$i] = explode('|', $selectorsArr[$i]);

	if(count($selectorsArr[$i]) < 2)
		$selectorsArr[$i][1] = null;
}
$output = file_get_contents($url) or die('Could not access: ?url=...');

phpQuery::newDocument($output);


$mess = filterAll($selectorsArr);

$clean = rearrange($mess);

$output = new stdClass();
$output->url = replaceThisByThatInThat('\\','',$url);
$output->sel = $sel;

$output->results = $clean;

$encoded = json_encode($output);

$encoded = replaceThisByThatInThat('\\/', '/', $encoded);


if($debug)
{
	var_dump($output);
}
else echo( $encoded );


function filterAll($selectors)
{
	$all = array();

	foreach($selectors as $i => $oneSelector)
	{
		$all[$i] = (filterthis($oneSelector[0], $oneSelector[1]) );
	}

	return $all;
}

// takes arrays[0][X] and arrays[1][X] to arrays[X][0,1]
function rearrange($arrays)
{
	$result = array();
	$temp = array();

	foreach($arrays as $onearr)
	{
		$temp = array_merge($temp, $onearr);
	}

	//$temp = array_merge($arrays[0], $arrays[1]);

	$nominal_length = count($arrays[0]);
	$total_arrays = count($arrays);

	for ($i=0; $i < $nominal_length; $i++)
	{
		for ($j=0; $j < $total_arrays; $j++)
		{
			try {
				$result[$i][$j] = $temp[$i+($nominal_length*$j)];
			} catch(Exception $e) {}
		}
	}

	return $result;
}


function filterThis($selector, $attribute = null)
{
	$arr = array();

	foreach(pq($selector) as $item)
	{
		if($attribute == null)
		{
			array_push($arr, rtrim(ltrim(pq($item)->text())));
		}
		else if($attribute == 'html')
		{
			array_push($arr, rtrim(ltrim(pq($item)->html())));
		}
		else
			array_push($arr, pq($item)->attr($attribute));
	}

	return $arr;
}

function replaceThisByThatInThat($this, $byThat, $inThat)
{
	return str_replace($this, $byThat, $inThat);
}

?>


