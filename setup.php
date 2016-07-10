<?php

add_action('init', 'wei_create_post_type');

function wei_create_post_type()
{
    register_post_type(
        WEI_POST_TYPE,
        array(
            'labels' => array(
                'name' => __('Notes'),
                'singular_name' => __('Note')
            ),
            'public' => true,
            'has_archive' => true,
            'taxonomies' => array('category', 'post_tag'),
        )
    );
}

add_filter('cron_schedules', 'wei_add_cron_schedule');
 
function wei_add_cron_schedule($schedules)
{
    if (!isset($schedules[WEI_SCHEDULE])) {
        $schedules[WEI_SCHEDULE] = array(
            'interval' => 300,
            'display'  => esc_html__('Every Five Minutes'),
        );
    }
 
    return $schedules;
}
