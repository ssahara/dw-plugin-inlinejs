<?php
/**
 * DokuWiki Syntax Plugin InlineJS Preloader
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:javascript
 *
 * Allow inline JavaScript in DW page. 
 * Make specified files to be loaded in head section of HTML by action component.
 *
 * SYNTAX:
 *         <PRELOAD debug>
 *           /path/to/javascript.js
 *           /path/to/stylesheet.css 
 *         </PRELOAD>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_inlinejs_preloader extends DokuWiki_Syntax_Plugin {

    protected $special_pattern  = '<PRELOAD.*?</PRELOAD>';

    public function getType()  { return 'protected'; }
    public function getPType() { return 'block'; }
    public function getSort()  { return 110; }
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->special_pattern,$mode,
            implode('_', array('plugin',$this->getPluginName(),$this->getPluginComponent(),))
        );
    }

 /**
  * handle syntax
  */
    public function handle($match, $state, $pos, Doku_Handler &$handler) {

        $match = substr($match,8,-10);  // strip markup without '>' in open tag
        $opts = array( // set default
                     'debug'  => false,
                );

        // check whether optional parameter exists
        if ( substr($match,0,1) != '>') {
            list($param, $match) = explode('>',$match, 2);
            if (preg_match('/debug/',$param)) {
                $opts['debug'] = true;
            }
            $opts['debug'] = true;
        } else {
            $match = substr($match,1); // strip '>' in open tag
        }

        $matches = explode("\n", $match);
        $n = count($matches);
        //$files[] = array();
        for ($i=0; $i<$n; $i++) {
            $match = trim($matches[$i]);
            // remove comment line after "#"
            list($filepath, $comment) = explode('#', $match, 2);
            if ( !empty($filepath) ) $files[] = $filepath;
        }
        return array($state, $opts, $files);
    }

 /**
  * Render metadata
  */
    public function render($format, Doku_Renderer &$renderer, $data) {

        global $ID, $conf;
        define("BR", "<br />\n");
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) return false;

        list($state, $opts, $files) = $data;

        switch ($format) {
            case 'metadata' :
                // metadata will be treated by action plugin
                $renderer->meta['plugin_inlinejs'] = implode('|',$files);
                return true;
                break;
            case 'xhtml' :
                if (!$opts['debug']) return false;
                $meta = p_get_metadata($ID, 'plugin_inlinejs');
                
                // debug information: show what js/css is to be loaded in head section
                $items = explode('|',$meta);
                $html = '<div class="notify">';
                $html.= hsc($this->getLang('preloader-intro')).BR;
                foreach  ($items as $metaentry) {
                    $p = strrpos($metaentry, '.');
                    if ($p !== false) {
                        $metatype = substr($metaentry, $p-strlen($metaentry));
                        $metatype = strtolower($metatype);
                    } else $metatype = '';
                    $html.= '['.$metatype.'] '.$metaentry.BR;
                }
                $html.= '</div>'.NL;
                $renderer->doc.=$html;
                return true;
                break;
        }
        return false;
    }
}
