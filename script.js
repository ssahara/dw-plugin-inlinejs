/*
 * script.js : inlinejs plugin for DokuWiki
 * replace "~~REMOTE_ADDR~~" and "~~SERVER_ADDR~~" macro
 * http://www.dokuwiki.org/plugin:inlinejs
 * @author Satoshi Sahara <sahara.satoshi@gmail.com>
 */

jQuery(function() {
    if (typeof JSINFO.server == 'undefined') return;
    jQuery('p:contains(" ~~REMOTE_ADDR~~"), li:contains(" ~~REMOTE_ADDR~~"), table:contains(" ~~REMOTE_ADDR~~")').each(function(){
        var txt = jQuery(this).html();
        jQuery(this).html(
            txt.replace(/ ~~REMOTE_ADDR~~/g,JSINFO.server.REMOTE_ADDR)
        );
    });
    jQuery('p:contains(" ~~SERVER_ADDR~~"), li:contains(" ~~SERVER_ADDR~~"), table:contains(" ~~SERVER_ADDR~~")').each(function(){
        var txt = jQuery(this).html();
        jQuery(this).html(
            txt.replace(/ ~~SERVER_ADDR~~/g,JSINFO.server.SERVER_ADDR)
        );
    });
});

