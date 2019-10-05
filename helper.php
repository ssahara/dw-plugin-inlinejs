<?php
/**
 * Helper Component for the InlineJS Plugin
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class helper_plugin_inlinejs extends DokuWiki_Plugin
{
    /**
     * render HTML of inline JavaScript
     */
    public function renderInlineJsHtml(Doku_Renderer $renderer, $script)
    {
        if (empty($script)) return false;

        $html  = '<script type="text/javascript">'.DOKU_LF.'/*<![CDATA[*/'.DOKU_LF;
        $html .= $script;
        $html .= '/*!]]>*/'.DOKU_LF.'</script>'.DOKU_LF;
        $renderer->doc .= $html;
        return true;
    }

}
