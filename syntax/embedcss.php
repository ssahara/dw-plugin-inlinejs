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

class syntax_plugin_inlinejs_embedcss extends syntax_plugin_inlinejs_embedder
{
    public function getType()
    {   // Syntax Type
        return 'protected';
    }

    public function getPType()
    {   // Paragraph Type
        return 'block';
    }

    /**
     * Connect pattern to lexer
     */
    //protected $mode, $pattern;

    public function preConnect()
    {
        // drop 'syntax_' from class name
        $this->mode = substr(get_class($this), 7);

        // syntax pattern
        $this->pattern[1] = '<CSS>(?=.*?</CSS>)';
        $this->pattern[4] = '</CSS>';
    }

    /**
     * Plugin features
     */
    //protected $code = null;

    /**
     * Create output
     */
    public function render($format, Doku_Renderer $renderer, $data)
    {
        list($state, $code) = $data;
        if ($format != 'xhtml') return false;

        $html = '<style type="text/css">'.DOKU_LF.'<!-- ';
        $html.= $code;  // raw write
        $html.= ' -->'.DOKU_LF.'</style>'.DOKU_LF;
        $renderer->doc .= $html;

        return true;
    }

}
