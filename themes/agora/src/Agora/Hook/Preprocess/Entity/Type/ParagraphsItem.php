<?php
/**
 * Created by Adam Jakab.
 * Date: 27/10/16
 * Time: 13.18
 */

namespace Agora\Hook\Preprocess\Entity\Type;

use Agora\Hook\Hook;
use Agora\Hook\HookInterface;

class ParagraphsItem extends Hook implements HookInterface
{
    /**
     * @param array $vars
     */
    public static function execute(&$vars)
    {
        self::setThemeHookSuggestions($vars);
        //dpm($vars['theme_hook_suggestions'], "PIE THS");
    }
    
    
    /**
     * @param array $vars
     */
    private static function setThemeHookSuggestions(&$vars)
    {
        $bundle = $vars['paragraphs_item']->bundle;
        $bundleContextFieldName = $vars['paragraphs_item']->field_name;
        $viewMode = $vars['view_mode'];
        
        $vars['theme_hook_suggestions'] = [];
        $vars['theme_hook_suggestions'][] = 'paragraphs_item';
        $vars['theme_hook_suggestions'][] = 'paragraphs_item__' . $bundle;
        $vars['theme_hook_suggestions'][] = 'paragraphs_item__' . $bundle . '__' . $viewMode;
        $vars['theme_hook_suggestions'][] = 'paragraphs_item__' . $bundle . '__' . $bundleContextFieldName;
        $vars['theme_hook_suggestions'][] = 'paragraphs_item__' . $bundle . '__' . $bundleContextFieldName . '__' . $viewMode;
    }
    
    
}