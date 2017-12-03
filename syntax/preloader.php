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
 *           <style>...</style>
 *           <script src="//example.con/javascript.js"></script>
 *           <script>...</script>
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
        $this->pattern[21] = '<link [^\n\r]*?>';
        $this->pattern[22] = '<style>.*?</style>';
        $this->pattern[23] = '<script\b[^\n\r]*?>.*?</script>';
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
        $this->Lexer->addPattern($this->pattern[21], $this->mode);
        $this->Lexer->addPattern($this->pattern[22], $this->mode);
        $this->Lexer->addPattern($this->pattern[23], $this->mode);
    }


    /**
     * add an entry to dedicated class property 
     */
    private function _add_entry($tag, $data='') {
        switch ($tag) {
            case 'link':
                $this->entries[] = array(
                             '_tag' => 'link',
                             'rel'  => 'stylesheet',
                          // 'type' => 'text/css',
                             'href' => $data,
                );
                break;
            case 'style':
                $this->entries[] = array(
                             '_tag' => 'style',
                          // 'type' => 'text/css',
                             '_data' => $data,
                );
                break;
            case 'script':
                $this->entries[] = array(
                             '_tag' => 'script',
                          // 'type' => 'text/javascript',
                             '_data'=> $data,
                );
                break;
            case 'js':
                $this->entries[] = array(
                             '_tag' => 'script',
                          // 'type' => 'text/javascript',
                             'src'  => $data,
                             '_data'=> '',
                );
                break;
        }
        return count($this->entries);
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
                if ('link' == substr($match, 1, 4)) {
                    // assume rel="stylesheet", lazy handling of external css
                    if (preg_match('/\bhref=\"([^\"]*)\" ?/', $match, $attrs)) {
                        $this->_add_entry('link', $attrs[1]);
                    }
                } elseif ('style' == substr($match, 1, 5)) {
                    $declarations = substr($match, 7, -8);
                    if (!empty($declarations)) {
                        $this->_add_entry('style', $declarations);
                    }
                } elseif ('script' == substr($match, 1, 6)) {
                    if (preg_match('/\bsrc=\"([^\"]*)\" ?/', $match, $attrs)) {
                        $this->_add_entry('js', $attrs[1]);
                    } else {
                        $source = substr($match, 8, -9);
                        if (!empty($source)) {
                            $this->_add_entry('script', $source);
                        }
                    }
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
                    if (in_array($entrytype, array('css','js'))) {
                        $tag = ($entrytype == 'css') ? 'link' : 'js';
                        $this->_add_entry($tag, $pathname);
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
                    foreach ($entries as $entry) {
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
