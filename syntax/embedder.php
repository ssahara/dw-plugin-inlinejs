<?php
/**
 * DokuWiki Syntax Plugin InlineJS Embedder
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:javascript
 *
 * Allow inline JavaScript in DokuWiki page. 
 * This plugin ensures that your script embedded inside of CDATA section.
 *
 * SYNTAX:
 *         <javascript>
 *           ... 
 *         </javascript>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_inlinejs_embedder extends DokuWiki_Syntax_Plugin {

    protected $mode, $pattern;

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_'

        // syntax pattern
        $this->pattern[1] = '<javascript>(?=.*?</javascript>)';
        $this->pattern[4] = '</javascript>';
    }

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 305; }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addEntryPattern($this->pattern[1], $mode, $this->mode);
    }
    function postConnect() {
        $this->Lexer->addExitPattern($this->pattern[4], $this->mode);
    }

    /**
     * handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {

        global $conf;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) {
            msg($this->getPluginName().': '.$this->getPluginComponent().' is disabled.',-1);
            return false;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                return array($state,'');

            case DOKU_LEXER_UNMATCHED:
                return array($state, $match);

            case DOKU_LEXER_EXIT:
                return array($state, '');
        }
        return false;
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {

        list($state, $code) = $data;
        if ($format != 'xhtml') return false;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $html = '<script type="text/javascript">'.DOKU_LF.'/*<![CDATA[*/';
                $renderer->doc .= $html;
                break;

            case DOKU_LEXER_UNMATCHED:
                //$renderer->doc .= $renderer->_xmlEntities($code);
                $renderer->doc .= $code; // raw write
                break;

            case DOKU_LEXER_EXIT:
                $html = '/*!]]>*/'.DOKU_LF.'</script>'.DOKU_LF;
                $renderer->doc .= $html;
                break;
        }
        return true;
    }
}
