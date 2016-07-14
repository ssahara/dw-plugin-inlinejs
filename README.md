DokuWiki plugin InlineJS
========================

Allow internal JavaScript and StyleSheet in wiki pages.


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


----
Licensed under the GNU Public License (GPL) version 2

More information is available:
  * https://www.dokuwiki.org/plugin:inlinejs

(c) 2014-2016 Satoshi Sahara \<sahara.satoshi@gmail.com>

