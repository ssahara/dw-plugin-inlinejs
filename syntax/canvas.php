<?php
/**
 * DokuWiki Syntax Plugin Canvas
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:javascript
 *
 * define plot container (canvas) for various JavaScript Chart software.
 *
 * SYNTAX:
 *         {{canvas software_name> name chartid [size]}}
 *    Ex1) {{canvas jqplot> chart1 400px,300px ]}
 *    Ex2) {{canvas RGraph> chart2 500px,300px ]}
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

class syntax_plugin_inlinejs_canvas extends DokuWiki_Syntax_Plugin {
    function getType()  { return 'substition'; }
    function getPType() { return 'normal'; }
    function getSort()  { return 160; }
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{canvas[^}\n]+}}',$mode,'plugin_inlinejs_canvas');
    }

 /**
  * handle syntax
  */
    public function handle($match, $state, $pos, &$handler){

        $match = substr($match,8,-2);  // strip markup
        $opts = array( // set default
                     'plotter' => '',
                     'chartid' => '',
                     'width'   => '400px',
                     'height'  => '300px',
                     );

        if ( substr($match,0,1) != '>') { // check first char
            list($param, $match) = explode('>',$match, 2);
            $opts['plotter'] = trim($param);
        } else {
            $match = substr($match,1); // last 1 strip markup
        }

        $match = trim($match);
        $tokens = preg_split('/\s+/', $match);
        foreach ($tokens as $token) {

            // get width and height of canvas
            $matches=array();
            if (preg_match('/(\d+(%|em|pt|px)?)\s*([,xX]\s*(\d+(%|em|pt|px)?))?/',$token,$matches)){
                if ($matches[4]) {
                    // width and height was given
                    $opts['width'] = $matches[1];
                    if (!$matches[2]) $opts['width'].= 'px'; //default to pixel when no unit was set
                    $opts['height'] = $matches[4];
                    if (!$matches[5]) $opts['width'].= 'px'; //default to pixel when no unit was set
                    continue;
                } elseif ($matches[2]) {
                    // only height was given
                    $opts['height'] = $matches[1];
                    if (!$matches[2]) $opts['height'].= 'px'; //default to pixel when no unit was set
                    continue;
                }
            }
            // get chartid, first match prioritized
            //restrict token characters to prevent any malicious chartid
            if (preg_match('/[^A-Za-z0-9_-]/',$token)) continue;
            if (empty($opts['chartid'])) $opts['chartid'] = $token;
        }
        return array($state, $opts);
    }

 /**
  * Render plot container (canvas)
  */
    public function render($mode, &$renderer, $data) {

        if ($mode != 'xhtml') return false;

        list($state, $opts) = $data;

        // check whether chartid defined?
        if (empty($opts['chartid'])) return false;

        switch ($opts['plotter']) {
            case 'jqplot':
                $canvastag = 'div';
                break;
            case 'RGraph':
                $canvastag = 'canvas';
                break;
        }

        $html = '<'.$canvastag.' id="'.$opts['chartid'].'" class="tpl_canvas"';
        $html.= ' style="';
        if ($opts['width'])  { $html.= 'width: '.$opts['width'].'; '; }
        if ($opts['height']) { $html.= 'height: '.$opts['height'].'; '; }
        $html.= '">';
        if ($canvastag == 'canvas') $html.= '[No canvas support]';
        $html.= '</'.$canvastag.'>'.NL;
        $renderer->doc.=$html;
        return true;
    }
}
