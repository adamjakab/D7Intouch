<?php
/**
 * Created by Adam Jakab.
 * Date: 20/10/16
 * Time: 9.50
 */

namespace Agora\Alter\Form;

use Mekit\Drupal7\HookInterface;

/**
 * Class NodeEdit
 *
 * @package Mekit\Drupal7\Alter\Form
 */
class NodeEdit implements HookInterface
{
    /**
     * @param array $form
     * @param array $form_state
     */
    public static function execute(&$form, $form_state)
    {
        self::limitAccess($form);
        
        //dpm($form, "NODE-EDIT FORM");
    }

    /**
     * @param array $form
     */
    private static function limitAccess(&$form)
    {
        global $user;
        if ($user->uid != 1) {
            // Not admin
            $form['options']['promote']['#access'] = false;
            $form['options']['sticky']['#access'] = false;
            $form['revision_information']['#access'] = false;
        }
    }
}