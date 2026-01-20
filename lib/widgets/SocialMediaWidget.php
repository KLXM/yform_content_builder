<?php

namespace FriendsOfREDAXO\YFormContentBuilder\Widgets;

/**
 * Social Media Widget
 * 
 * Fügt Social Media Links (Facebook, Instagram, LinkedIn, Twitter, YouTube) hinzu.
 * Demo-Widget für das Widget-System.
 */
class SocialMediaWidget extends ContentBuilderWidgetAbstract
{
    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return 'social_media';
    }
    
    /**
     * @inheritDoc
     */
    public static function getLabel(): string
    {
        return 'Social Media Links';
    }
    
    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Fügt Social Media Links (Facebook, Instagram, LinkedIn, Twitter, YouTube) hinzu.';
    }
    
    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [
            'social_facebook' => [
                'type' => 'text',
                'label' => 'Facebook URL',
                'notice' => 'z.B. https://facebook.com/meinseite'
            ],
            'social_instagram' => [
                'type' => 'text',
                'label' => 'Instagram URL',
                'notice' => 'z.B. https://instagram.com/meinaccount'
            ],
            'social_linkedin' => [
                'type' => 'text',
                'label' => 'LinkedIn URL',
                'notice' => 'z.B. https://linkedin.com/company/meinunternehmen'
            ],
            'social_twitter' => [
                'type' => 'text',
                'label' => 'Twitter/X URL',
                'notice' => 'z.B. https://twitter.com/meinaccount'
            ],
            'social_youtube' => [
                'type' => 'text',
                'label' => 'YouTube URL',
                'notice' => 'z.B. https://youtube.com/@meinkanal'
            ],
            'social_show' => [
                'type' => 'checkbox',
                'label' => 'Social Media Links anzeigen'
            ]
        ];
    }
    
    /**
     * @inheritDoc
     */
    public function getHookName(): string
    {
        return 'after_content';
    }
    
    /**
     * @inheritDoc
     */
    public function render(array $widgetData, string $framework = 'bootstrap'): string
    {
        $showSocial = $widgetData['social_show'] ?? false;
        
        if (!$showSocial) {
            return '';
        }
        
        $socialLinks = [
            'facebook' => [
                'url' => $widgetData['social_facebook'] ?? '',
                'icon' => 'fa-facebook',
                'label' => 'Facebook'
            ],
            'instagram' => [
                'url' => $widgetData['social_instagram'] ?? '',
                'icon' => 'fa-instagram',
                'label' => 'Instagram'
            ],
            'linkedin' => [
                'url' => $widgetData['social_linkedin'] ?? '',
                'icon' => 'fa-linkedin',
                'label' => 'LinkedIn'
            ],
            'twitter' => [
                'url' => $widgetData['social_twitter'] ?? '',
                'icon' => 'fa-twitter',
                'label' => 'Twitter'
            ],
            'youtube' => [
                'url' => $widgetData['social_youtube'] ?? '',
                'icon' => 'fa-youtube',
                'label' => 'YouTube'
            ]
        ];
        
        // Filter leere Links
        $socialLinks = array_filter($socialLinks, function($link) {
            return !empty($link['url']);
        });
        
        if (empty($socialLinks)) {
            return '';
        }
        
        return $this->renderLinks($socialLinks, $framework);
    }
    
    /**
     * Rendert die Social Media Links
     * 
     * @param array $links
     * @param string $framework
     * @return string
     */
    private function renderLinks(array $links, string $framework): string
    {
        $output = '';
        
        switch ($framework) {
            case 'uikit':
                $output .= '<div class="uk-margin">';
                $output .= '<div class="uk-grid-small uk-child-width-auto" uk-grid>';
                
                foreach ($links as $platform => $link) {
                    $output .= '<div>';
                    $output .= '<a href="' . $this->escape($link['url']) . '" target="_blank" rel="noopener noreferrer" class="uk-icon-button" uk-icon="' . $this->escape($platform) . '" aria-label="' . $this->escape($link['label']) . '">';
                    $output .= '<i class="fa ' . $this->escape($link['icon']) . '"></i>';
                    $output .= '</a>';
                    $output .= '</div>';
                }
                
                $output .= '</div>';
                $output .= '</div>';
                break;
                
            case 'bootstrap':
            default:
                $output .= '<div class="social-media-widget">';
                
                foreach ($links as $platform => $link) {
                    $output .= '<a href="' . $this->escape($link['url']) . '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-default" aria-label="' . $this->escape($link['label']) . '">';
                    $output .= '<i class="fa ' . $this->escape($link['icon']) . '"></i> ';
                    $output .= $this->escape($link['label']);
                    $output .= '</a> ';
                }
                
                $output .= '</div>';
                break;
        }
        
        return $output;
    }
}
