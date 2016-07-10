<?php

if (!wp_next_scheduled(WEI_CRON)) {
    wp_schedule_event(time(), WEI_SCHEDULE, WEI_CRON);
}

add_action(WEI_CRON, 'wei_cron');

function wei_cron()
{
    wei_get_notes();
}

function wei_run_action()
{
    if (!is_user_logged_in()) {
        return false;
    }

    if (isset($_POST[WEI_ACTION_LATEST])) {
        wei_get_notes();
    }

    if (isset($_POST[WEI_ACTION_OLDER])) {
        wei_get_notes(false);
    }
}

add_action('init', 'wei_run_action');

function wei_get_note_updated_timestamp($last = true)
{
    $args = array(
        'post_type' => WEI_POST_TYPE,
        'post_status' => 'any',
        'orderby' => 'modified',
    );

    if ($last) {
        $args['order'] = 'DESC';
    } else {
        $args['order'] = 'ASC';
    }

    $posts = get_posts($args);

    if (count($posts) === 0) {
        return false;
    } else {
        $modified_date = $posts[0]->post_modified;
        return date('U', strtotime($modified_date));
    }
}

function get_latest_notes($last = true) {
    $search_term = WEI_SEARCH;

    if ($last && $updated = wei_get_note_updated_timestamp($last)) {
        $search_term .= ' updated:' . $updated;
    }

    $client = new \Evernote\Client(WEI_DEVELOPER_TOKEN, WEI_SANDBOX, null, null, false);
    $search = new \Evernote\Model\Search($search_term); // Search String
    $notebook = new \Evernote\Model\Notebook();
    $scope = \Evernote\Client::SEARCH_SCOPE_BUSINESS; // Search scope
    $order = \Evernote\Client::SORT_ORDER_RECENTLY_UPDATED; // Search order
    $max_results = 5;

    $results = $client->findNotesWithSearch($search, $notebook, $scope, $order, $max_results);

    // TODO: pagination

    return $results;
}

function get_note_content($guid) {
    global $note_store;

    $content = $note_store->getNoteContent(WEI_DEVELOPER_TOKEN, $guid);

    return $content;
}

function get_note_tags($guid) {
    global $note_store;

    $content = $note_store->getNoteTagNames(WEI_DEVELOPER_TOKEN, $guid);

    return $content;
}

function get_notes($last = true) {
    $return_notes = array();
    $notes = get_latest_notes($last = true);

    foreach ($notes as $result) {
        $note = array(
            'guid' => $result->guid,
            'title' => $result->title,
            'created' => $result->created,
            'updated' => $result->updated,
        );

        $content = get_note_content($result->guid);

        if($content) {
            $note['content'] = $content;
        }

        $tags = get_note_tags($result->guid);

        if($tags) {
            $note['tags'] = $tags;
        }

        $return_notes[] = $note;
    }

    if(count($return_notes)) {
        return $return_notes;
    } else {
        return null;
    } 
}  

function wei_get_note_by_guid($guid) {
    $args = array(
        'post_type' => WEI_POST_TYPE,
        'post_status' => 'any',
        'meta_key' => 'guid',
        'meta_value' => $guid,
    );

    $posts = get_posts($args);

    if (count($posts) === 0) {
        return false;
    } else {
        $post_id = $posts[0]->ID;
        return $post_id;
    }
}

function wei_get_notes($last = true) {
    global $client, $note_store;

    $options = wei_get_keys();

    if (isset($options[WEI_APP_KEY_ID])) {
        define('WEI_DEVELOPER_TOKEN', $options[WEI_APP_KEY_ID]);
    } else {
        return false;
    }

    if (isset($options[WEI_APP_SECRET_ID])) {
        $search = $options[WEI_APP_SECRET_ID];
    } else {
        $search = false;
    }

    define('WEI_SEARCH', $search);

    require_once(WEI_PLUGIN_PATH . 'vendor/autoload.php');
    $client = new \Evernote\AdvancedClient(WEI_DEVELOPER_TOKEN, WEI_SANDBOX);
    $note_store = $client->getNoteStore();  

    $notes = get_notes($last);

    if($notes) {
        foreach($notes as $note) {
            $post_data = array(
                'post_status' => 'publish',
                'post_type' => WEI_POST_TYPE,
                'post_title' => $note['title'],
            );

            if ($id = wei_get_note_by_guid($note['guid'])) {
                $post_data['ID'] = $id;
            }

            $created = substr($note['created'], 0, -3);
            $created = date('Y-m-d H:i:s', $created);
            $post_data['post_date'] = $created;

            $updated = substr($note['updated'], 0, -3);;
            $updated = date('Y-m-d H:i:s', $updated);
            $post_data['post_modified'] = $updated;

            $content = $note['content'];
            $post_data['post_content'] = $content;

            $post_data['meta_input']['guid'] = $note['guid'];

            if (isset($note['tags'])) {
                $tags = array();

                foreach ($note['tags'] as $tag) {
                    $tags[] = $tag;
                }

                if (count($tags)) {
                    $post_data['tax_input']['post_tag'] = $tags;
                }
            }

            $post_id = wp_insert_post($post_data);
        }
    }
}
