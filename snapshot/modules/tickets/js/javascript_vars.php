<?php
 // variables which are required in both viewticket and submitticket.
 if (!function_exists('get_lang')) exit;
?>
<script>
    var fileInputs, limit = <?php echo $attachmentSettings['attachment_limit']; ?>;
    var allowedExtensions = <?php echo json_encode(explode(', ', $attachmentSettings['attachment_extensions'])); ?>;
    var maxFileSize = <?php echo $attachmentSettings['attachment_max_size']; ?>;
    var maxFileSizeUnits = "<?php echo bytesTo($attachmentSettings['attachment_max_size']); ?>";
    var fixBeforeSubmitting = "<?php echo get_lang('ticket_fix_before_submitting'); ?>"
    var fixBeforeReplying = "<?php echo get_lang('ticket_fix_before_replying'); ?>"
    var problemWithAttachments = "<?php echo get_lang('ticket_problem_with_attachments'); ?>"
    var invalidExtensionLang = "<?php echo get_lang('ticket_attachment_invalid_extension'); ?>"
    var invalidSizeLang = "<?php echo get_lang('ticket_attachment_invalid_size'); ?>"
    var maxFileElements = "<?php echo get_lang('ticket_max_file_elements'); ?>"
    var multipleFilesSelects = "<?php echo get_lang('ticket_attachment_multiple_files'); ?>"
    var extensionsLang = "<?php echo get_lang('attachment_allowed_extensions_info'); ?>"

<?php
    if ($attachmentSettings['attachment_limit'] == 0) {
        echo 'var fileSizeInfo = "'. get_lang_f('attachment_size_info', bytesTo($attachmentSettings['attachment_max_size'])) .'"';
    } else {
        echo 'var fileSizeInfo = "'. get_lang_f('attachment_file_size_info', $attachmentSettings['attachment_limit'], bytesTo($attachmentSettings['attachment_max_size'])) .'"';
    }
?>
</script>