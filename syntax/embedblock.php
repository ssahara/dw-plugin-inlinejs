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

    protected $entry_pattern    = '<JS>(?=.*?</JS>)';
    protected $exit_pattern     = '</JS>';

    public function getPType() { return 'block'; }
}
