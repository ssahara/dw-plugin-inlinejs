<?php
/**
 * DokuWiki Syntax Plugin InlineJS EmbedCss
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
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
     * Connect pattern to lexer
     */
    function render($format, Doku_Renderer $renderer, $indata) {

        if (empty($indata)) return false;
        list($state, $data) = $indata;
        if ($format != 'xhtml') return false;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $html = '<style type="text/css">'.DOKU_LF.'<!-- ';
                $renderer->doc .= $html;
                break;

            case DOKU_LEXER_UNMATCHED:
                //$renderer->doc .= $renderer->_xmlEntities($data);
                $renderer->doc .= $data; // raw write
                break;

            case DOKU_LEXER_EXIT:
                $html = ' -->'.DOKU_LF.'</style>'.DOKU_LF;
                $renderer->doc .= $html;
                break;
        }
        return true;
    }

}
