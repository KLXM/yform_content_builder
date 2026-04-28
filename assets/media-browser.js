/**
 * YForm Content Builder - Media Browser
 * Verwendet REDAXO Standard Medienpool (openREXMedia)
 * Diese Datei ist nur noch für Kompatibilität vorhanden
 */

(function($) {
    'use strict';

    // Leeres Objekt für Abwärtskompatibilität
    var MediaBrowser = {
        init: function() {
            // Nichts zu tun - REDAXO Medienpool wird verwendet
        }
    };

    $(document).ready(function() {
        MediaBrowser.init();
    });

    // Globale Referenz für Abwärtskompatibilität
    window.MediaBrowser = MediaBrowser;
    window.ContentBuilderMediaBrowser = MediaBrowser;

})(jQuery);
