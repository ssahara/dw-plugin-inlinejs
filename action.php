<?php
/**
 * DokuWiki Action Plugin InlineJS
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 */
// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_inlinejs extends DokuWiki_Action_Plugin {

    // register hook
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, '_exportToJSINFO');
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'inlinejs_handleMeta');
    }

    /**
     * export $_SERVER to JSINFO
     *
     */
    public function _exportToJSINFO(Doku_Event $event, $param) {
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
    public function inlinejs_handleMeta(Doku_Event $event, $param) {

        global $ID, $INFO;

        $metadata = $INFO['meta']['plugin_inlinejs'];
        if (!$metadata) return;
        $items = explode('|',$metadata);

        foreach ($items as $entry) {
            // check file name extention
            $entrytype = pathinfo($entry, PATHINFO_EXTENSION);
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
