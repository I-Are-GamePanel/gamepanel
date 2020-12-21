<?php

require 'include/functions.php';

require 'include/TicketSettings.php';
require 'includes/form_table_class.php';

function exec_ogp_module()
{
    global $db, $view;

    $TicketSettings = new TicketSettings($db);
    $errors = array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $types = array('kb', 'mb', 'gb', 'tb', 'pb');
        $fields = array();    

        $ratings_enabled = (int)$_POST['ratings_enabled'];
        $attachments_enabled = (int)$_POST['attachments_enabled'];
        $notifications_enabled = (int)$_POST['notifications_enabled'];
        $attachment_limit = (int)$_POST['attachment_limit'];
        $attachment_extensions = trim($_POST['attachment_extensions']);
        $extensions = splitExtensions($attachment_extensions);

        $fields['ratings_enabled'] = ($ratings_enabled >= 1 ? 1 : 0);
        $fields['attachments_enabled'] = ($attachments_enabled >= 1 ? 1 : 0);
        $fields['notifications_enabled'] = ($notifications_enabled >= 1 ? 1 : 0);
        $fields['attachment_limit'] = $attachment_limit;

        if (!is_numeric(substr($_POST['attachment_max_size'], 0, -2))) {
            $errors[] = get_lang('invalid_max_size');
        } elseif (!in_array(strtolower(substr($_POST['attachment_max_size'], -2)), $types)) {
            $errors[] = get_lang('invalid_unit');
        } else {
            $fields['attachment_max_size'] = toBytes($_POST['attachment_max_size']);
        }

        if (!is_dir($_POST['attachment_save_dir']) && !mkdir($_POST['attachment_save_dir'], 0777, true)) {
            $errors[] = get_lang('invalid_save_dir');
        } elseif (!is_writable($_POST['attachment_save_dir'])) {
            $errors[] = get_lang('invalid_save_dir_not_writable');
        } else {
            $fields['attachment_save_dir'] = $_POST['attachment_save_dir'];
        }

        if (empty($attachment_extensions) || empty($extensions)) {
            $errors[] = get_lang('invalid_extensions');
        } else {
            $fields['attachment_extensions'] = $extensions;
        }

        $TicketSettings->set($fields);
    }
    
    $settings = $TicketSettings->get();

    echo '<h2>'.get_lang('ticket_settings').'</h2>';

    if (!empty($errors)) {
        echo ticketErrors($errors, get_lang('settings_errors_occured'));
    }

    $form = new FormTable;
    $form->start_form('?m=tickets&p=ticket_settings', 'POST');
    $form->start_table();

    $form->add_field('on_off', 'ratings_enabled', $settings['ratings_enabled']);
    $form->add_field('on_off', 'attachments_enabled', $settings['attachments_enabled']);
    $form->add_field('on_off', 'notifications_enabled', $settings['notifications_enabled']);
    $form->add_field('string', 'attachment_max_size', bytesTo($settings['attachment_max_size']));
    $form->add_field('string', 'attachment_limit', $settings['attachment_limit']);
    $form->add_field('string', 'attachment_save_dir', $settings['attachment_save_dir']);
    $form->add_field('string', 'attachment_extensions', $settings['attachment_extensions']);

    $form->end_table();
    $form->add_button('submit', 'update_settings', get_lang('update_settings'));
    $form->end_form();
?>
    <button id="phpIniButton"><?php echo get_lang('show_php_ini'); ?></button>
    <div id="guesstimateIniSettings"></div>

    <script src="modules/tickets/js/ticket_settings.js"></script>
<?php
}