DokuWiki plugin InlineJS
========================

Allow adhoc internal JavaScript and StyleSheet in wiki pages.


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
    /path/to/some.js     # depends on document root of web server
    /path/to/some.css
    <script src="http://example.com/javascript.js"></script>         # external JavaScript
    <link rel="stylesheet" href="http://example.com/css?key=value">  # external css
    <style>
      ... css rule-set ...
    </style>
    </PRELOAD>


----
Licensed under the GNU Public License (GPL) version 2

More information is available:
  * https://www.dokuwiki.org/plugin:inlinejs

(c) 2014-2019 Satoshi Sahara \<sahara.satoshi@gmail.com>

