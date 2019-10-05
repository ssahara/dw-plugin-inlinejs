<?php
/**
 * DokuWiki Action Plugin InlineJS
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 */
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class action_plugin_inlinejs extends DokuWiki_Action_Plugin
{
    // register hook
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'BEFORE', $this, 'inlinejs_handleMeta');
    }


    /**
     * TPL_METAHEADER_OUTPUT
     * add inline javascript and/or stylesheet to the <head> section.
     */
    public function inlinejs_handleMeta(Doku_Event $event, $param)
    {
        global $INFO;

        if (!isset($INFO['meta']['plugin_inlinejs'])) return;

        foreach ($INFO['meta']['plugin_inlinejs'] as $entry) {
            switch ($entry['_tag']) {
                case 'style':
                    $event->data['style'][] = array(
                            'type'    => 'text/css',
                            '_data'   => $entry['_data'],
                    );
                    break;
                case 'link':
                    $event->data['link'][] = array(
                            'rel'     => 'stylesheet',
                            'type'    => 'text/css',
                            'href'    => $entry['href'],
                    );
                    break;
                case 'script':
                    if (isset($entry['src'])) {
                        $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                            'charset' => 'utf-8',
                            'src'    => $entry['src'],
                            '_data'   => '',
                        );
                    } else {
                        $event->data['script'][] = array(
                            'type'    => 'text/javascript',
                         // 'charset' => 'utf-8',
                            '_data'   => $entry['_data'],
                        );
                    }
                    break;
            }
        }
        return;
    }
}
