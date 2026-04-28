<?php

use KLXM\YFormContentBuilder\YformListProfiles;

/**
 * API: Spalten einer YForm-Tabelle für die Profil-Konfiguration laden.
 *
 * Aufruf: /redaxo/index.php?rex-api-call=yform_list_columns&table=rex_news
 */
class rex_api_yform_list_columns extends rex_api_function
{
    protected $published = false;

    public function execute(): rex_api_result
    {
        if (!rex::isBackend() || !rex::getUser()) {
            rex_response::setStatus(rex_response::HTTP_FORBIDDEN);
            rex_response::cleanOutputBuffers();
            rex_response::sendJson(['error' => 'forbidden']);
            exit;
        }

        rex_response::cleanOutputBuffers();

        $table = (string) rex_request('table', 'string', '');
        $columns = '' !== $table ? YformListProfiles::collectColumns($table) : [];
        $urlProfiles = YformListProfiles::collectUrlProfiles($table);

        rex_response::sendJson([
            'table' => $table,
            'columns' => $columns,
            'url_profiles' => $urlProfiles,
        ]);
        exit;
    }
}
