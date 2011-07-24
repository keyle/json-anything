<?php
/**
* JSON-ANYTHING
* Scrape any webpage and returns a JSON object
*
* @version 1.0
* @author Nicolas 'keyle' Noben
* @license MIT License
*
* See README for syntax
*
**/

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; U; ru; rv:5.0.1.6) Gecko/20110501 Firefox/5.0.1 Firefox/5.0.1');
require_once('phpQuery-onefile.php');



// Cleaning Request Parameters (url, sel for selectors, debug for debug flag)
$debug = isset($_REQUEST['debug']) ? true : false;
$url = parseParam('url');
$sel = parseParam('sel');
$sel = replaceThisByThatInThat('__', ' ', $sel);
$sel = replaceThisByThatInThat('%', '#', $sel);
$array_selectors = getIndividiualSelectors($sel);
$array_selectors = separateSelectorsAndAttributes($array_selectors);


// Get the page and do the filtering
$output = file_get_contents($url);
phpQuery::newDocument($output);
$oneAfterTheOtherArrays = phpQueryFilterAllSelectors($array_selectors);
$ordered = dumbMergeArrays($oneAfterTheOtherArrays);


// Create JSON output
$output = new stdClass();
$output->url = replaceThisByThatInThat('\\','',$url);
$output->sel = $sel;
$output->results = $ordered;

$json_encoded_string = json_encode($output);
$json_encoded_string = replaceThisByThatInThat('\\/', '/', $json_encoded_string);


if($debug)
	var_dump($output);
else
	echo($json_encoded_string);



function parseParam($param)
{
	if(!$_REQUEST[$param])
		die('MISSING parameter: ' . $param);

	return filterNastyStuff($_REQUEST[$param]);
}

function getIndividiualSelectors($commaDelimited)
{
	return explode(',', $commaDelimited);
}

function separateSelectorsAndAttributes($array_selectors)
{
	for ($i=0; $i < count($array_selectors); $i++)
	{
		$array_selectors[$i] = explode('|', $array_selectors[$i]);

		if(count($array_selectors[$i]) < 2)
			$array_selectors[$i][1] = null;
	}

	return $array_selectors;
}

// WARNING: not great.
// if - for example - we're getting titles and links, and one of the results does not have a link, this will skip a beat!!
// in other words, the final JSON results might skip a beat if one of the selectors, parsing the page, is not found
// results can get out of whack quickly.
function phpQueryFilterAllSelectors($selectors)
{
	$all = array();

	foreach($selectors as $i => $oneSelector)
	{
		$all[$i] = (filterthis($oneSelector[0], $oneSelector[1]) );
	}

	return $all;
}

// does a phpQuery applying the selector, with conditions on the attribute
function filterThis($selector, $attribute = null)
{
	$arr = array();

	foreach(pq($selector) as $item)
	{
		if($attribute == null) // if no attribute, return text by default
		{
			array_push($arr, rtrim(ltrim(pq($item)->text())));
		}
		else if($attribute == 'html') // if html, return html content
		{
			array_push($arr, rtrim(ltrim(pq($item)->html())));
		}
		else // otherwise we've been give an attribute (src, href, alt, title...)
			array_push($arr, pq($item)->attr($attribute));
	}

	return $arr;
}

// takes arrays[0][X] and arrays[1][X], return arrays[X][0,1]
// Warning: FRAGILE! phpQueryFilterAllSelectors probably needs to be smarter.
// used when we have more than one selector, the results arrays are given one selector at a time
// we use it to merge so that titles and links (for example) go together.
// example:
// takes   [ [title, title, title], [link, link, link], [name, name, name] ]
// returns [ [title, link, name], [title, link, name], [title, link, name] ]
// Warning: Dumb code! if one of the link is missing, it will skip a beat!
function dumbMergeArrays($arrays)
{
	$result = array();
	$temp = array();

	foreach($arrays as $onearr)
	{
		$temp = array_merge($temp, $onearr);
	}

	$nominal_length = count($arrays[0]);
	$total_arrays = count($arrays);

	for ($i=0; $i < $nominal_length; $i++)
	{
		for ($j=0; $j < $total_arrays; $j++)
		{
			try {
				$result[$i][$j] = $temp[ $i + ($nominal_length * $j) ];
			} catch(Exception $e) {}
		}
	}

	return $result;
}

// simple shorthand for the obfuscated str_replace
function replaceThisByThatInThat($this, $byThat, $inThat)
{
	return str_replace($this, $byThat, $inThat);
}

// killing a bird with a shotgun - improve me if you can
function filterNastyStuff($stuff) {
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

?>


