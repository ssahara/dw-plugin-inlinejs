<?php
/**
 * DokuWiki Syntax Plugin InlineJS Embedder
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Satoshi Sahara <sahara.satoshi@gmail.com>
 *
 * @see also: https://www.dokuwiki.org/devel:javascript
 *
 * Allow inline JavaScript in DokuWiki page. 
 * This plugin ensures that your script embedded inside of CDATA section.
 *
 * SYNTAX:
 *         <javascript>
 *           ... 
 *         </javascript>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_inlinejs_embedder extends DokuWiki_Syntax_Plugin
{
    public function getType()
    {   // Syntax Type
        return 'protected';
    }

    public function getPType()
    {   // Paragraph Type
        return 'block';
    }

    /**
     * Connect pattern to lexer
     */
    protected $mode, $pattern;

    public function preConnect()
    {
        // drop 'syntax_' from class name
        $this->mode = substr(get_class($this), 7);

        // syntax pattern
        $this->pattern[1] = '<javascript>(?=.*?</javascript>)';
        $this->pattern[4] = '</javascript>';
    }

    public function connectTo($mode)
    {
        $this->Lexer->addEntryPattern($this->pattern[1], $mode, $this->mode);
    }

    public function postConnect()
    {
        $this->Lexer->addExitPattern($this->pattern[4], $this->mode);
    }

    public function getSort()
    {   // sort number used to determine priority of this mode
        return 305;
    }

    /**
     * Plugin features
     */
    protected $code = null;


    /**
     * handle the match
     */
    public function handle($match, $state, $pos, Doku_Handler $handler)
    {
        global $conf;

        if ($this->getConf('follow_htmlok') && !$conf['htmlok']) {
            msg($this->getPluginName().': '.$this->getPluginComponent().' is disabled.',-1);
            return false;
        }

        switch ($state) {
            case DOKU_LEXER_ENTER:
                return false;

            case DOKU_LEXER_UNMATCHED:
                $this->code = $match;
                return false;

            case DOKU_LEXER_EXIT:
                $data = array($state, $this->code);
                $this->code = null;

                if ($this->getConf('follow_htmlok') && !$conf['htmlok']) {
                    $msg = $this->getPluginComponent().' is disabled.';
                    msg($this->getPluginName().': '.$msg, -1);
                    return false;
                }
                return $data;
        }
        return false;
    }

    /**
     * Create output
     */
    public function render($format, Doku_Renderer $renderer, $data)
    {
        list($state, $code) = $data;
        if ($format != 'xhtml') return false;

        $html = '<script type="text/javascript">'.DOKU_LF.'/*<![CDATA[*/';
        $html.= $code;  // raw write
        $html.= '/*!]]>*/'.DOKU_LF.'</script>'.DOKU_LF;
        $renderer->doc .= $html;

        return true;
    }
}
