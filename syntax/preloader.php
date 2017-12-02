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
 *           <link rel="stylesheet" href="//example.com/css">
 *         </PRELOAD>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_inlinejs_preloader extends DokuWiki_Syntax_Plugin {

    protected $mode, $pattern;
    protected $entries = array();

    function __construct() {
        $this->mode = substr(get_class($this), 7); // drop 'syntax_'

        // syntax pattern
        $this->pattern[1] = '<PRELOAD\b[^\n\r]*?>(?=.*?</PRELOAD>)';
        $this->pattern[2] = '<link [^\n\r]*?>';
        $this->pattern[4] = '</PRELOAD>';
    }

    function getType()  { return 'protected'; }
    function getPType() { return 'block'; }
    function getSort()  { return 110; }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addEntryPattern($this->pattern[1], $mode, $this->mode);
    }
    function postConnect() {
        $this->Lexer->addExitPattern($this->pattern[4], $this->mode);
        $this->Lexer->addPattern($this->pattern[2], $this->mode);
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, Doku_Handler $handler) {

        switch ($state) {
            case DOKU_LEXER_ENTER:
                // initialize class property
                $this->entries = array();

                // check whether optional parameter exists
                $opts['debug'] = (preg_match('/debug/',$match)) ? true : false;
                if ($match != '<PRELOAD>') { $opts['debug'] = true; }
                $this->entries['debug'] = $opts['debug'];
                return false;

            case DOKU_LEXER_MATCHED:
                // assume rel="stylesheet", lazy handling of external css
                if (preg_match('/\bhref=\"([^\"]*)\" ?/', $match, $attrs)) {
                    $this->entries[] = array(
                             '_tag' => 'link',
                             'rel'  => 'stylesheet',
                          // 'type' => 'text/css',
                             'href' => $data,
                    );
                }
                return false;

            case DOKU_LEXER_UNMATCHED:
                $matches = explode("\n", $match);
                foreach ($matches as $entry) {
                    // remove comment line after "#"
                    list($pathname, $comment) = explode('#', $entry, 2);
                    $pathname = trim($pathname);

                    // check entry type for loacl file path
                    $entrytype = strtolower(pathinfo($pathname, PATHINFO_EXTENSION));
                    if (!in_array($entrytype, array('css','js'))) {
                        continue;
                    } elseif ($entrytype == 'css') {
                        $this->entries[] = array(
                             '_tag' => 'link',
                             'rel'  => 'stylesheet',
                          // 'type' => 'text/css',
                             'href' => $data,
                        );
                    } elseif ($entrytype == 'js') {
                        $this->entries[] = array(
                             '_tag' => 'script',
                          // 'type' => 'text/javascript',
                             'src'  => $data,
                             '_data'=> '',
                        );
                    }
                }
                return false;

            case DOKU_LEXER_EXIT:
                return $data = $this->entries;
        }
    }

    /**
     * Create output
     */
    function render($format, Doku_Renderer $renderer, $data) {

        global $ID, $conf;
        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) return false;

        $entries =& $data;

        switch ($format) {
            case 'metadata' :
                unset($entries['debug']);
                // metadata will be treated by action plugin
                $renderer->meta['plugin_inlinejs'] = $entries;
                return true;

            case 'xhtml' :
                if ($entries['debug']) {
                    unset($entries['debug']);
                    // debug: show what js/css is to be loaded in head section
                    $html = '<div class="notify">';
                    $html.= hsc($this->getLang('preloader-intro')).DOKU_LF;
                    foreach ($data as $entry) {
                        $attr = buildAttributes($entry);
                        $out = '<'.$entry['_tag'].($attr ? ' '.$attr : '');
                        if (isset($entry['_data'])) {
                            $out.= '>'.$entry['_data'].'</'.$entry['_tag'].'>';
                        } else {
                            $out.= '>';
                        }
                        $html.= '<div style="white-space:pre; padding:0.1em;">'.hsc($out).'</div>'.DOKU_LF;
                    }
                    $html.= '</div>'.DOKU_LF;
                    $renderer->doc .= $html;
                }
                return true;
        }
        return false;
    }
}
