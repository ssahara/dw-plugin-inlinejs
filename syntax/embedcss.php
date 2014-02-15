<?php
/**
 * DokuWiki Syntax Plugin InlineJS EmbedCss
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:css
 *
 * Allow inline StyleSheet in DW page. 
 *
 * SYNTAX:
 *         <CSS>
 *           ... 
 *         </CSS>
 */

require_once(dirname(__FILE__).'/embedder.php');

class syntax_plugin_inlinejs_embedcss extends syntax_plugin_inlinejs_embedder {

    protected $entry_pattern    = '<CSS>(?=.*?</CSS>)';
    protected $exit_pattern     = '</CSS>';

    function getPType() { return 'block'; }


 /**
  * Render <script> element
  */
    public function render($mode, &$renderer, $indata) {

        if (empty($indata)) return false;
        list($state, $data) = $indata;
        if ($mode != 'xhtml') return false;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $html = '<style type="text/css">'.NL.'<!-- ';
                $renderer->doc .= $html;
                break;

            case DOKU_LEXER_UNMATCHED:
                //$renderer->doc .= $renderer->_xmlEntities($data);
                $renderer->doc .= $data; // raw write
                break;

            case DOKU_LEXER_EXIT:
                $html = ' -->'.NL.'</style>'.NL;
                $renderer->doc .= $html;
                break;
        }
        return true;
    }

}
