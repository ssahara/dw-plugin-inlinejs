<?php
/**
 * DokuWiki Syntax Plugin InlineJS Embedder
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
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
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_inlinejs_embedder extends DokuWiki_Syntax_Plugin {

    protected $entry_pattern    = '<javascript>(?=.*?</javascript>)';
    protected $exit_pattern     = '</javascript>';
    protected $special_pattern  = '<javascript src=.*?/>';

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 305; }
    function connectTo($mode) {
        $this->Lexer->addEntryPattern($this->entry_pattern,$mode,
            implode('_', array('plugin',$this->getPluginName(),$this->getPluginComponent(),))
        );
        $this->Lexer->addSpecialPattern($this->special_pattern,$mode,
            implode('_', array('plugin',$this->getPluginName(),$this->getPluginComponent(),))
        );
    }
    function postConnect() {
        $this->Lexer->addExitPattern($this->exit_pattern,
            implode('_', array('plugin',$this->getPluginName(),$this->getPluginComponent(),))
        );
    }

 /**
  * handle the match
  */
    public function handle($match, $state, $pos, &$handler){

        global $conf;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) {
            msg($this->getPluginName().': JavaScript embedding is disabled.',-1);
            return false;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                return array($state,'');

            case DOKU_LEXER_UNMATCHED:
                return array($state, $match);

            case DOKU_LEXER_EXIT:
                return array($state, '');

            case DOKU_LEXER_SPECIAL:
                return array($state, $match);
        }
        return false;
    }

 /**
  * Render <script> element
  */
    public function render($mode, &$renderer, $indata) {

        if (empty($indata)) return false;
        list($state, $data) = $indata;
        if ($mode != 'xhtml') return false;

        switch ($state) {
            case DOKU_LEXER_ENTER:
                $html = '<script type="text/javascript">'.NL.'/*<![CDATA[*/';
                $renderer->doc .= $html;
                break;

            case DOKU_LEXER_UNMATCHED:
                //$renderer->doc .= $renderer->_xmlEntities($data);
                $renderer->doc .= $data; // raw write
                break;

            case DOKU_LEXER_EXIT:
                $html = '/*!]]>*/'.NL.'</script>'.NL;
                $renderer->doc .= $html;
                break;

            case DOKU_LEXER_SPECIAL:
                if (preg_match('|src="?(.+\.js)"?[ /]|', $data, $matches)) {
                    $html = '<script type="text/javascript" src="'.$matches[1].'"></script>'.NL;
                    $renderer->doc .= $html;
                }
                break;
        }
        return true;
    }
}
