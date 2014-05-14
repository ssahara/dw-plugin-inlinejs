<?php
/**
 * DokuWiki Action Plugin InlineJS
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
require_once (DOKU_INC.'inc/parserutils.php');

/**
 * All DokuWiki plugins to interfere with the event system
 * need to inherit from this class
 */
class action_plugin_inlinejs extends DokuWiki_Action_Plugin {

    // register hook
    function register(&$controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, '_exportToJSINFO');
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'inlinejs_handleMeta');
    }

    /**
     * export $_SERVER to JSINFO
     *
     */
    public function _exportToJSINFO(&$event) {
        global $JSINFO;
        //$JSINFO['server'] = $_SERVER;
        $JSINFO['server'] = array(
            'SERVER_NAME' => $_SERVER['SERVER_NAME'],
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
            'REMOTE_USER' => $_SERVER['REMOTE_USER'],
        );
    }

    /**
     * add inline javascript and/or stylesheet to the <head> section.
     *
     */
    function inlinejs_handleMeta(&$event, $param) {

        global $ID, $INFO;
        if (!$INFO['exists']) return;

        $metakey = 'plugin_inlinejs';
        $meta = p_get_metadata($ID, 'plugin_inlinejs', false);
        if (empty($meta)) return;
        $items = explode('|',$meta);

        foreach ($items as $entry) {

            // check file name extention
            $p = strrpos($entry, '.');
            if ($p !== false) {
                $entrytype = substr($entry, $p-strlen($entry));
                $entrytype = strtolower($entrytype);
            } else $entrytype = '';

            switch ($entrytype) {
            
                case ".css":
                    $event->data['link'][] = array(
                            'rel'     => 'stylesheet',
                            'type'    => 'text/css',
                            'href'    => $entry,
                    );
                    break;
                case ".js":
                default:
                    $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            'src'    => $entry,
                            '_data'   => '',
                     );
                    break;
            }
        }
    }
}
