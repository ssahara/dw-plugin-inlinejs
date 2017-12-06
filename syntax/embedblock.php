<?php
/**
 * DokuWiki Syntax Plugin InlineJS EmbedBlock
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
 *         <JS>
 *           ... 
 *         </JS>
 */

require_once(dirname(__FILE__).'/embedder.php');

class syntax_plugin_inlinejs_embedblock extends syntax_plugin_inlinejs_embedder {

  //protected $mode, $pattern;
  //protected $code = null;

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_'

        // syntax pattern
        $this->pattern[1] = '<JS>(?=.*?</JS>)';
        $this->pattern[4] = '</JS>';
    }

    function getPType() { return 'block'; }
}
