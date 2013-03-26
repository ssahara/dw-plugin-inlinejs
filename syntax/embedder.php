<?php
/**
 * DokuWiki Syntax Plugin InlineJS Embedder
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
 *         <JS>
 *           ... 
 *         </JS>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_inlinejs_embedder extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 305; }
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<JS>.+?</JS>',$mode,'plugin_inlinejs_embedder');
    }

 /**
  * handle syntax
  */
    function handle($match, $state, $pos, &$handler){

        $match = substr($match,4,-5);  // strip markup
        return array($state, $match);
    }

 /**
  * Render inline javascript
  */
    public function render($mode, &$renderer, $data) {

        global $conf;
        if ($mode != 'xhtml') return false;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) return false;

        list($state, $script) = $data;
        if ( $script =='') return false;

        $html = '<script type="text/javascript">'.NL.'/*<![CDATA[*/'.NL;
        $html.= $script;
        $html.= '/*!]]>*/'.NL.'</script>'.NL;
        $renderer->doc.=$html;
        return true;
    }
}
