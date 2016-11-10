<?php
/**
 * Created by Adam Jakab.
 * Date: 20/10/16
 * Time: 9.50
 */

namespace Agora\Preprocess;

use Agora\Util\ThemeHelper;
use Mekit\Drupal7\HookInterface;

class Page implements HookInterface
{
    /**
     * @param array $vars
     */
    public static function execute(&$vars)
    {
        self::killNoContentSystemMessage($vars);
        self::generateArticleNavbar($vars);
        
        //dpm($vars["page"], "PAGE VARS");
    }
    
    private static function generateArticleNavbar(&$vars)
    {
        if(isset($vars["node"]))
        {
            /** @var \stdClass $node */
            $node = $vars["node"];
            
            if(in_array($node->type, ["standard", "long", "event"]))
            {
                //dpm($node, "PAGE NODE");
                
                $customlink1 = false;
                
                //Event has special google calendar link
                if($node->type == "event")
                {
                    //dpm($node, "N");
                    $customlink1 = "#";
                    if(isset($node->field_event_date[LANGUAGE_NONE][0]['value']) && isset
                        ($node->field_event_date[LANGUAGE_NONE][0]['value2']))
                    {
                        // Title
                        $title = $node->title;
    
                        // Details
                        $details = '';
    
                        // Location
                        $location = "";
                        if(isset($node->field_event_location['und'][0]['value']))
                        {
                            $location = $node->field_event_location['und'][0]['value'];
                        }
    
                        // Dates
                        dpm($node->field_event_date[LANGUAGE_NONE][0]);
                        $timeZone = new \DateTimeZone($node->field_event_date[LANGUAGE_NONE][0]['timezone']);
                        $startDate = new \DateTime($node->field_event_date[LANGUAGE_NONE][0]['value'], $timeZone);
                        $finishDate = new \DateTime($node->field_event_date[LANGUAGE_NONE][0]['value2'], $timeZone);
    
                        $customlink1 = ThemeHelper::createGoogleCalendarInsertUri($title, $details, $location, $startDate, $finishDate);
                    }
                }
                
    
                $title = $node->title_field[LANGUAGE_NONE][0]['value'];
                
                $parentNid = false;
                $parentTitle = false;
                $parentIssueNumber = false;
                if(isset($node->nodehierarchy_menu_links[0]['pnid']))
                {
                    $parentNid = $node->nodehierarchy_menu_links[0]['pnid'];
                    $parentNode = node_load($parentNid);
                    if($parentNode)
                    {
                        //dpm($parentNode, "PARENT");
                        $parentTitle = $parentNode->title_field[LANGUAGE_NONE][0]['value'];
                        $parentIssueNumber = $parentNode->field_pubnumber[LANGUAGE_NONE][0]['value'];
                    }
                }
    
    
                $agoraNavBar = [
                    '#theme' => 'agoranav',
                    '#backlink' => url('<front>', []),
                    '#issue' => 'ISSUE #' . $parentIssueNumber,
                    '#article_title' => $title,
                ];
                
                if($customlink1) {
                    $agoraNavBar['#customlink1'] = $customlink1;
                }
    
                $vars['page']['content']['navbar'] = $agoraNavBar;
                
                // When Article navbar is present - we remove the primary navigation
                unset($vars['page']['wide_top']['system_main-menu']);
                
            }
        }
    }
    
    private static function killNoContentSystemMessage(&$vars)
    {
        if (drupal_is_front_page()) {
            if(isset($vars['page']['content']['system_main']['default_message'])) {
                unset($vars['page']['content']['system_main']['default_message']);
            }
        }
    }

}