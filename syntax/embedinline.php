<?php
/**
 * DokuWiki Syntax Plugin InlineJS EmbedInline
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
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

    protected $entry_pattern = '<js>(?=.*?</js>)';
    protected $exit_pattern  = '</js>';

    function getPType() { return 'normal'; }
}
