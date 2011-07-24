json-anything
----------------

PHP proxy that converts webpages to json objects, filtered by jquery-like selectors.

Takes parameters from GET or POST.

Think of it as a 'shotgun api'.

Copyright (c) 2011 Nicolas Noben. see LICENSE (MIT).

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

PHP can't parse # from a url as it's never sent to the server ('url fragments'). Because of this, use % instead.

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

	%results__td:nth-child(4)__span:nth-child(2)__b|html :

**FULL EXAMPLE:**
Get the latest currency prices from Reuters

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
