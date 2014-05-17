DokuWiki plugin InlineJS
========================

Allow inline JavaScript and StyleSheet in pages. Support both preloading in `<head>` section and embedding in `<body>` section of HTML.


Syntax
------

Embedding inline JavaScript (Use uppercase tags if you need to enclose block level elements.) 

    <JS>
    ... javascript...  (block type output)
    </JS>

    <js>... javascript...</js>  (output does not break paragraph)

    <CSS>
    ... style sheet...  (block type output)
    </CSS>


Let some library files preloaded in specific DokuWiki pages

    <PRELOAD>
    /path/to/some.js
    /path/to/some.css
    </PRELOAD>


Macro those replaced by Javascript: Put a space char in front of macro strings to enable replacement in which the space char is removed. 

```
  ~~SERVER_ADDR~~
  ~~REMOTE_ADDR~~
```


----
Licensed under the GNU Public License (GPL) version 2

More information is available:
  * https://www.dokuwiki.org/plugin:inlinejs

(c) 2014 Satoshi Sahara \<sahara.satoshi@gmail.com>

