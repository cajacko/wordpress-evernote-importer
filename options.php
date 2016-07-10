<?php

add_action('admin_menu', 'wei_admin_add_page');

function wei_admin_add_page()
{
    add_options_page(WEI_PLUGIN_NAME, WEI_PLUGIN_NAME, 'manage_options', WEI_PLUGIN_ID, 'wei_options_page');
}

function wei_options_page()
{
    ?>
    <div>
        <form action="options.php" method="post">
            <?php settings_fields(WEI_OPTIONS_SLUG); ?>
            <?php do_settings_sections(WEI_PLUGIN_ID); ?>
             
            <input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
        </form>

        <h2>Actions</h2>

        <form action="" method="POST">
            <input type="hidden" name="<?php echo WEI_ACTION_LATEST; ?>" value="true" />
            <input type="submit" value="<?php esc_attr_e('Get Latest Notes'); ?>" />
        </form>

        <form action="" method="POST">
            <input type="hidden" name="<?php echo WEI_ACTION_OLDER; ?>" value="true" />
            <input type="submit" value="<?php esc_attr_e('Get Older Notes'); ?>" />
        </form>
    </div>
    <?php
}

add_action('admin_init', 'wei_admin_init');

function wei_admin_init()
{
    register_setting(WEI_OPTIONS_SLUG, WEI_OPTIONS_SLUG, 'wei_options_validate');
    add_settings_section(WEI_OPTIONS_SECTION, WEI_PLUGIN_NAME, 'wei_section_text', WEI_PLUGIN_ID);
    add_settings_field(WEI_APP_KEY_ID, 'Developer Token', 'wei_token_setting_string', WEI_PLUGIN_ID, WEI_OPTIONS_SECTION);
    add_settings_field(WEI_APP_SECRET_ID, 'Search Query', 'wei_search_setting_string', WEI_PLUGIN_ID, WEI_OPTIONS_SECTION);
}

function wei_section_text()
{
    // echo '<p>Add in your Evernote details here.</p>';
}

function wei_token_setting_string()
{
    $options = get_option(WEI_OPTIONS_SLUG);
    echo "<input id='" . WEI_APP_KEY_ID . "' name='" . WEI_OPTIONS_SLUG . "[" . WEI_APP_KEY_ID . "]' size='40' type='text' value='{$options[WEI_APP_KEY_ID]}' />";
}

function wei_search_setting_string()
{
    $options = get_option(WEI_OPTIONS_SLUG);
    echo "<input id='" . WEI_APP_SECRET_ID . "' name='" . WEI_OPTIONS_SLUG . "[" . WEI_APP_SECRET_ID . "]' size='40' type='text' value='{$options[WEI_APP_SECRET_ID]}' />";
}

function wei_options_validate($input)
{
    return $input;
}

function wei_get_keys()
{
    $options = get_option(WEI_OPTIONS_SLUG);

    if (!isset($options[WEI_APP_KEY_ID])) {
        return false;
    }

    if (strlen($options[WEI_APP_KEY_ID]) < 5) {
        return false;
    }

    return $options;
}
