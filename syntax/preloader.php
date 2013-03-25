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
 * Make specified files to be loaded in head section of HTML by action compornent.
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
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 110; }
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<PRELOAD.*?</PRELOAD>',$mode,'plugin_inlinejs_preloader');
    }

 /**
  * handle syntax
  */
    function handle($match, $state, $pos, &$handler){

        $match = substr($match,8,-10);  // strip markup
        $opts = array( // set default
                     'debug'  => false,
                );

        // $matchの先頭が'>'以外では、何らかのオプション指定あり
        if ( substr($match,0,1) != '>') {
            list($param, $match) = explode('>',$match, 2);
            if (preg_match('/debug/',$param)) {
                $opts['debug'] = true;
            }
            $opts['debug'] = true;
        } else {
            $match = substr($match,1);
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
    public function render($mode, &$renderer, $data) {

        global $ID, $conf;
        list($state, $opts, $files) = $data;
        if ($conf['follow_htmlok'] && !$conf['htmlok']) return false;

        switch ($mode) {
            case 'metadata' :
                // metadata will be treated by action plugin
                $renderer->meta['plugin_inlinejs'] = implode('|',$files);
                return true;
                break;
            case 'xhtml' :
                if(!$opts['debug']) return false;
                resolve_pageid($ID, $id, $exists);
                $meta = p_get_metadata($id, 'plugin_inlinejs');
                
                // debug information: show what js/css is to be loaded in head section
                $items = explode('|',$meta);
                $html = '<pre>'.NL;
                $html.= 'REMARK: This page uses additional css and/or js file(s).'.NL;
                $html.= htmlspecialchars('<PRELOAD>').NL;
                foreach  ($items as $metaentry) {
                    $p = strrpos($metaentry, '.');
                    if ($p !== false) {
                        $metatype = substr($metaentry, $p-strlen($metaentry));
                        $metatype = strtolower($metatype);
                    } else $metatype = '';
                    $html.= '['.$metatype.'] '.$metaentry.NL;
                }
                $html.= htmlspecialchars('</PRELOAD>').NL;
                $html.= '</pre>'.NL;
                $renderer->doc.=$html;
                return true;
                break;
        }
        return false;
    }
}
