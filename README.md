DokuWiki plugin InlineJS
========================

Allow inline JavaScript in pages. Support both preloading in `<head>` section and embedding in `<body>` section of HTML.


Syntax
------

Embedding inline JavaScript

    <JS>
    ... javascript...  (block type output)
    </JS>

    <js>... javascript...</js>  (output does not break paragraph)


Let some library files preloaded in specific DokuWiki pages

    <PRELOAD>
    /path/to/some.js
    /path/to/some.css
    </PRELOAD>

----
Licensed under the GNU Public License (GPL) version 2

More information is available:
  * https://www.dokuwiki.org/plugin:inlinejs

(c) 2014 Satoshi Sahara \<sahara.satoshi@gmail.com>

