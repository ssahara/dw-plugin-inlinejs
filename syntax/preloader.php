<?php
/**
 * DokuWiki Syntax Plugin InlineJS Preloader
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
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

class syntax_plugin_inlinejs_preloader extends DokuWiki_Syntax_Plugin {

    protected $mode, $pattern;

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_'
        $this->pattern[5] = '<PRELOAD\b.*?</PRELOAD>';
    }

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 110; }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern($this->pattern[5], $mode, $this->mode);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {

        $match = substr($match, 8, -10);  // strip markup without '>' in open tag
        $opts = array( // set default
                     'debug'  => false,
                );

        // check whether optional parameter exists
        if ( substr($match, 0, 1) != '>') {
            list($param, $match) = explode('>',$match, 2);
            if (preg_match('/debug/',$param)) {
                $opts['debug'] = true;
            }
            $opts['debug'] = true;
        } else {
            $match = substr($match, 1); // strip '>' in open tag
        }

        $matches = explode("\n", $match);
        $n = count($matches);
        $files = array();
        for ($i=0; $i<$n; $i++) {
            // remove comment line after "#"
            list($filepath, $comment) = explode('#', $matches[$i], 2);
            $filepath = trim($filepath);
            if ( !empty($filepath) ) $files[] = $filepath;
        }
        return array($state, $opts, $files);
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {

        global $ID, $conf;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) return false;

        list($state, $opts, $files) = $data;

        switch ($format) {
            case 'metadata' :
                // metadata will be treated by action plugin
                $renderer->meta['plugin_inlinejs'] = implode('|', $files);
                return true;

            case 'xhtml' :
                if (!$opts['debug']) return false;
                $meta = p_get_metadata($ID, 'plugin_inlinejs');

                // debug information: show what js/css is to be loaded in head section
                $items = explode('|', $meta);
                $html  = '<div class="notify">';
                $html .= hsc($this->getLang('preloader-intro')).'<br />'.DOKU_LF;
                foreach ($items as $entry) {
                    // check file name extention
                    $entrytype = pathinfo($entry, PATHINFO_EXTENSION);
                    if (is_null($entrytype)) $entrytype = '';
                    $html .= '['.$entrytype.'] '.$entry.'<br />'.DOKU_LF;
                }
                $html .= '</div>'.DOKU_LF;
                $renderer->doc .= $html;
                return true;
        }
        return false;
    }
}
