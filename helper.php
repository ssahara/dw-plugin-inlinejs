<?php
/**
 * Helper Component for the InlineJS Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author  Sahara Satoshi <sahara.satoshi@gmail.com>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_inlinejs extends DokuWiki_Plugin {

    /**
     * Returns some documentation of the methods provided by this helper part
     *
     * @return array Method description
     */
    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'renderInlineJsHtml',
                'desc'   => 'render HTML of inline JavaScript',
                'params' => array(
                        'renderer'=>'renderer',
                        'script' => 'string'),
                'return' => array('html' => 'string'),
        );
        return $result;
    }

    /**
     * render HTML of inline JavaScript
     */
    function renderInlineJsHtml(&$renderer,$script) {
        if (empty($script)) return false;

        $html = '<script type="text/javascript">'.NL.'/*<![CDATA[*/'.NL;
        $html.= $script;
        $html.= '/*!]]>*/'.NL.'</script>'.NL;
        $renderer->doc.=$html;
        return true;
    }

}
