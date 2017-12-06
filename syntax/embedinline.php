<?php
/**
 * DokuWiki Syntax Plugin InlineJS EmbedInline
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:javascript
 *
 * Allow inline JavaScript in DW page. 
 * Make sure that your script embedded inside of CDATA section.
 *
 * SYNTAX:
 *         <js>
 *           ... 
 *         </js>
 */

require_once(dirname(__FILE__).'/embedder.php');

class syntax_plugin_inlinejs_embedinline extends syntax_plugin_inlinejs_embedder {

  //protected $mode, $pattern;
  //protected $code = null;

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_'

        // syntax pattern
        $this->pattern[1] = '<js>(?=.*?</js>)';
        $this->pattern[4] = '</js>';
    }

    function getPType() { return 'normal'; }
}
