<?php

/**
 * AJAX Handler für Media Browser
 * Stellt Endpunkte für Medienpool-Kategorien und -Listen bereit
 */
class yform_content_builder_ajax_handler
{
    /**
     * AJAX-Anfragen verarbeiten
     */
    public static function handle()
    {
        // Nur bei AJAX-Requests und im Backend
        if (!rex::isBackend() || !rex_request::isXmlHttpRequest()) {
            return;
        }
        
        $action = rex_request::post('action', 'string');
        
        switch ($action) {
            case 'load_media_categories':
                self::loadMediaCategories();
                break;
                
            case 'load_media_list':
                self::loadMediaList();
                break;
        }
    }
    
    /**
     * Medienpool-Kategorien als JSON ausgeben
     */
    protected static function loadMediaCategories()
    {
        $categories = [];
        
        // Kategorien aus Medienpool laden
        $categoryList = rex_media_category::getRootCategories();
        
        foreach ($categoryList as $category) {
            $categories[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ];
            
            // Unterkategorien rekursiv laden
            self::addSubcategories($category, $categories);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['categories' => $categories]);
        exit;
    }
    
    /**
     * Unterkategorien rekursiv hinzufügen
     */
    protected static function addSubcategories($parentCategory, &$categories)
    {
        $children = $parentCategory->getChildren();
        
        foreach ($children as $child) {
            $categories[] = [
                'id' => $child->getId(),
                'name' => str_repeat('— ', $child->getPath() ? count(explode('|', $child->getPath())) - 1 : 0) . $child->getName(),
            ];
            
            // Weitere Unterkategorien
            self::addSubcategories($child, $categories);
        }
    }
    
    /**
     * Medien-Liste als JSON ausgeben
     */
    protected static function loadMediaList()
    {
        $categoryId = rex_request::post('category_id', 'int', 0);
        $media = [];
        
        // SQL-Query für Medienpool
        $sql = rex_sql::factory();
        
        if ($categoryId > 0) {
            // Medien aus spezifischer Kategorie
            $sql->setQuery('SELECT filename, title, category_id, filetype FROM ' . rex::getTable('media') . ' WHERE category_id = ? ORDER BY filename', [$categoryId]);
        } else {
            // Alle Medien
            $sql->setQuery('SELECT filename, title, category_id, filetype FROM ' . rex::getTable('media') . ' ORDER BY filename');
        }
        
        while ($sql->hasNext()) {
            $media[] = [
                'filename' => $sql->getValue('filename'),
                'title' => $sql->getValue('title') ?: $sql->getValue('filename'),
                'category_id' => $sql->getValue('category_id'),
                'type' => $sql->getValue('filetype'),
            ];
            $sql->next();
        }
        
        header('Content-Type: application/json');
        echo json_encode(['media' => $media]);
        exit;
    }
}
