json-anything
----------------

PHP proxy that converts webpages to json objects, filtered by jquery-like selectors (Built on top of PHPQuery).

Takes parameters from GET or POST.

Think of it as a **'shotgun api'**. Because many sites still don't provide apis.

MIT LICENSE.

##Parameters:

###debug:

**This is a debug flag.**

If set, it will var_dump the results for improved readibility. If not, a json-encoded object is returned.

###url:

**The url to go scrape and make JSON from.**

For complex urls already using GET (ie. some search engines) you can POST url instead.

**EXAMPLE**

	?url=http://www.bom.gov.au/


###sel:

**Comma separated list of selectors, in a jQuery fashion**

PHP can't parse # from a url as it's never sent to the server ('url fragments').

Because of this, **use % instead of #**.

You can, but are not forced to, use two underscores (__) for a space (so it doesn't look like %20)

Get attributes by using a '|'

By default it will return 'text()'

	a        ---> will return all text of the A tags
	a|html   ---> will return all html contents of the A tags
	a|href   ---> will return all href attribute of the A tags
	img|src  ---> will return all src attribute of the img tags

**EXAMPLES**

	span.hey ---> will return all spans with class hey
	span%hey ---> will return all spans with id hey (I know)
	div%pad table:first a ---> div#pad table:first a

Complex Example using __ instead of space:

Within #results, will return the 4th column, 2nd span, b's html

	%results__td:nth-child(4)__span:nth-child(2)__b|html

##Real world examples

###Latest currencies $ from Reuters

	index.php?url=http://uk.reuters.com/business/currencies&sel=%currPairs__td:first-child__a,%currPairs__td:first-child__a|href,%currPairs__td:nth- child(2)

	{
	  "url":"http://uk.reuters.com/business/currencies",
	  "sel":"#currPairs td:first-child a,#currPairs td:first-child a|href,#currPairs td:nth-child(2)",
	  "results":[
	    [
	      "GBP/USD",
	      "/business/currencies/quote?srcAmt=1&srcCurr=GBP&destAmt=&destCurr=USD",
	      "1.6299"
	    ],
	    [
	      "GBP/EUR",
	      "/business/currencies/quote?srcAmt=1&srcCurr=GBP&destAmt=&destCurr=EUR",
	      "1.1348"
	    ],

	    ...
	  ]
	}

###Hacker News

There is an unofficial api but anyway, this works too.

	http://localhost/json-anything/?url=http://news.ycombinator.com/&sel=.title__a:first-child,.title__a:first-child|href

	{
	  "url":"http://news.ycombinator.com/",
	  "sel":".title a:first-child,.title a:first-child|href",
	  "results":[
	    [
	      "Octopress - A blogging framework for hackers",
	      "http://octopress.org/"
	    ],
	    [
	      "NoSQL is What?",
	      "http://blog.zawodny.com/2011/07/23/nosql-is-what/"
	    ],
	    [
	      "Radio Shack to start stocking Arduino, Other Goodies",
	      "http://blog.radioshack.com/2011/07/21/top-ten-diy-suggestions-from-you/"
	    ],

	    ...
	  ]
	}

###Australia Bureau of Meteorology (bom)

The bom is reknown for not having decent a api. 

Take a look at its home page http://www.bom.gov.au/ ... We're going to grab the Forecast as presented for the major cities.

	http://localhost/json-anything/?url=http://www.bom.gov.au/&sel=%pad__table:first__a,%pad__table:first__.max,%pad__table:first__td:last-child

	url=http://www.bom.gov.au/
	%pad__table:first__a, (grab the #pad then the first table's A tags text)
	%pad__table:first__.max, (the maximas, has class .max)
	%pad__table:first__td:last-child (grab the last TD of the first table)

	{
	  "url":"http://www.bom.gov.au/",
	  "sel":"#pad table:first a,#pad table:first .max,#pad table:first td:last-child",
	  "results":[
	    [
	      "Sydney",
	      "17\u00b0",
	      "Possible shower clearing."
	    ],
	    [
	      "Melbourne",
	      "14\u00b0",
	      "Shower or two."
	    ],
	    [
	      "Brisbane",
	      "23\u00b0",
	      "Fine."
	    ],

	    ...
	  ]
	}

(\u00b0 is the unicode for the DEGREE sign)

##Feel free to improve

Not much of this is perfect, it comes from flaws in its quirky design, also the PHP could be improved.

If you have some PHP experience in your legs and want to improve the code and/or add features, I'd gladly take changes (fork away!).
