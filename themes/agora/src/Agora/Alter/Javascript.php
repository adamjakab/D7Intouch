<?php
/**
 * Created by Adam Jakab.
 * Date: 20/10/16
 * Time: 9.50
 */

namespace Agora\Alter;

use Agora\Util\ThemeHelper;
use Mekit\Drupal7\HookInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Class Javascript
 *
 * @package Mekit\Drupal7\Alter
 */
class Javascript implements HookInterface
{
    /** @var  Parser */
    private static $parser;
    
    /**
     * @param array $js
     */
    public static function execute(&$js)
    {
        self::excludeAllNonAgoraJavascripts($js);
        self::includeJsForAgora($js);
    }
    
    
    /**
     * loads yaml file corresponding to the type|alias of current node
     *
     *
     */
    private static function includeJsForAgora(&$js)
    {
        $themePath = ThemeHelper::getCurrentThemePath();
        $baseConfigDir = $themePath . '/config/js/';
        /** @var \stdClass $menuItem */
        $menuItem = menu_get_object();
        
        $nodeType = isset($menuItem->type) ? $menuItem->type : false;
        $nodeId = isset($menuItem->nid) ? $menuItem->nid : false;
        
        // Site wide config
        $config = self::getParsedConfig($baseConfigDir . 'site.yml');
        $js = array_merge($js, $config);
        
        // Special Front page config
        if(drupal_is_front_page())
        {
            $config = self::getParsedConfig($baseConfigDir . 'front.yml');
            $js = array_merge($js, $config);
        }

        //Content type config
        if($nodeType)
        {
            $config = self::getParsedConfig($baseConfigDir . 'nodetype/' . $nodeType . '.yml');
            $js = array_merge($js, $config);
        }
    
        //Content Id config
        if($nodeId)
        {
            $config = self::getParsedConfig($baseConfigDir . 'nodeid/' . $nodeId . '.yml');
            $js = array_merge($js, $config);
        }
        
        //special - newsletter/topic/% - view 'Newsletter article topic'
        if(arg(0) == "newsletter" && arg(1) == "topic")
        {
            $config = self::getParsedConfig($baseConfigDir . 'special/newsletter_topic.yml');
            $js = array_merge($js, $config);
        }
        
        //dpm($js, "JS(FINAL)");
    }
    
    /**
     * @param string $path
     *
     * @return array
     */
    private static function getParsedConfig($path)
    {
        $answer = [];
        if(is_file($path))
        {
            self::$parser = self::$parser ? self::$parser : new Parser();
            $config = self::$parser->parse(file_get_contents($path));
            if (is_array($config) && isset($config["js"])) {
                $answer = $config["js"];
                $defaultElement = self::getDefaultJsElement();
                foreach($answer as $filepath => &$fileConfig)
                {
                    $fileConfig = array_merge($defaultElement, $fileConfig);
                    $fileConfig['data'] = $filepath;
                }
            }
        }
        
        return $answer;
    }
    
    /**
     * @see: includes/common.inc:4240
     *
     * @return array
     */
    private static function getDefaultJsElement()
    {
        return [
            'data' => '',
            'type' => 'file',
            'scope' => 'footer',
            'group' => JS_LIBRARY,
            'every_page' => FALSE,
            'weight' => -1,
            'requires_jquery' => TRUE,
            'preprocess' => TRUE,
            'cache' => TRUE,
            'defer' => FALSE,
        ];
    }
    
    /**
     * Tear away all js
     * @param array $js
     */
    private static function excludeAllNonAgoraJavascripts(&$js)
    {
        //dpm($js, "JS(BEFORE)");
        foreach ($js as $k => $v)
        {
            if ($k != 'settings')
            {
                unset($js[$k]);
            }
        }
        //dpm($js, "JS(AFTER)");
    }
}