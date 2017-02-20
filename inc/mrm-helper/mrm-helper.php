<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(class_exists('MrmHelper')) return;

class MrmHelper {

    static function getAvailablePostTypes()
    {
        $exclude = ['attachment','revision','nav_menu_item','_pods_pod','_pods_field','custom_css','customize_changeset'];
        $result = [];
        foreach (get_post_types( '', 'objects' ) as $key => $posttype)
        {
            if(!in_array($key, $exclude))
            {
                $result[] = $posttype;
            }
        }
        return $result;
    }

    static function sanitizeWithUnderscores($label)
    {
        return str_replace('-', '_', sanitize_title($label) );
    }
    
}