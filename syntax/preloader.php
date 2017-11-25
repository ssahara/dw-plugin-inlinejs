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
        $entries = array();
        foreach ($matches as $entry) {
            // remove comment line after "#"
            list($pathname, $comment) = explode('#', $entry, 2);
            $pathname = trim($pathname);
            if (empty($pathname) ) continue;

            // check entry type
            $entrytype = strtolower(pathinfo($pathname, PATHINFO_EXTENSION));
            if (in_array($entrytype, array('css','js'))) {
                $entries[] = array(
                    'type' => $entrytype,
                    'path' => $pathname,
                );
            }
        }
        return (count($entries)) ? array($opts, $entries) : false;
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {

        global $ID, $conf;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) return false;

        list($opts, $entries) = $data;

        switch ($format) {
            case 'metadata' :
                // metadata will be treated by action plugin
                $renderer->meta['plugin_inlinejs'] = $entries;
                return true;

            case 'xhtml' :
                if ($opts['debug']) {
                    // debug: show what js/css is to be loaded in head section
                    $html = '<div class="notify">';
                    $html.= hsc($this->getLang('preloader-intro')).'<br />';
                    foreach ($entries as $entry) {
                        $html.= '['.$entry['type'].'] '.$entry['path'].'<br />';
                    }
                    $html.= '</div>'.DOKU_LF;
                    $renderer->doc .= $html;
                }
                return true;
        }
        return false;
    }
}
