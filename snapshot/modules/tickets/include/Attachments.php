<?php

class Attachments
{
    private $db;

    private $filesArray = array();
    private $path;
    private $maxAttachments;
    private $maxSize;
    private $permittedExtensions = array();

    private $allMimeTypes = array();
    private $permittedMimeTypes = array();

    private $errors = array();
        
    public function __construct(OGPDatabase $db, $attachments, $path, $maxAttachments, $maxSize, $permittedExtensions)
    {
        $this->db = $db;

        $this->filesArray = $this->normalizeFiles($attachments);
        $this->path = $path;
        $this->maxAttachments = $maxAttachments;
        $this->maxSize = $maxSize;
        $this->permittedExtensions = $permittedExtensions;

        $this->allMimeTypes = require __DIR__ .'/mime.types.php';
        $this->createMimeArray();
    }

    public function validAttachmentCount()
    {
        if (!empty($this->filesArray)) {
            return ($this->maxAttachments == 0 || count($this->filesArray) <= $this->maxAttachments);
        }

        return null;
    }

    public function validate()
    {
        foreach ($this->filesArray as $i => $file) {
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $this->errors[$i][] = get_lang_f('attachment_err_ini_size', $file['name'], $file['size']);
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->errors[$i][] = get_lang_f('attachment_err_partial', $file['name']);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->errors[$i][] = get_lang_f('attachment_err_no_tmp', $file['name']);
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->errors[$i][] = get_lang_f('attachment_err_cant_write', $file['name']);
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->errors[$i][] = get_lang_f('attachment_err_extension', $file['name']);
                    break;
            }

            if ($this->checkSize($file['size'])) {
                $this->errors[$i][] = get_lang_f('attachment_too_large', $file['name'], $file['size'], $this->maxSize);
            }

            if (in_array($this->getMimeType($file['tmp_name']), $this->permittedMimeTypes) === false) {
                $this->errors[$i][] = get_lang_f('attachment_forbidden_type', $file['name']);
            }
        }

        return $this;
    }

    public function getErrors()
    {
        $errors = array();

        foreach ($this->errors as $error) {
            $errors = array_merge($error, $errors);
        }

        return $errors;
    }

    public function save($tid, $reply_id = null)
    {
        $savePath = (substr($this->path, -1) == '/' ? $this->path : $this->path . '/');

        foreach ($this->filesArray as $i => $file) {
            // Ignore and don't save file which has an error associated with it.
            if (array_key_exists($i, $this->errors)) {
                continue;
            }

            $original_name =  basename($file['name']);
            $extension     =  pathinfo($file['name'], PATHINFO_EXTENSION);

            $unique_name   =  bin2hex(openssl_random_pseudo_bytes(12)) . ( !$extension ? '' : '.' . $extension );

            move_uploaded_file($file['tmp_name'], $savePath . $unique_name);
            $this->insertAttachment($tid, $reply_id, $original_name, $unique_name);
        }
    }

    private function insertAttachment($tid, $reply_id, $original_name, $unique_name)
    {
        $fields = array(
            'ticket_id'     =>  $tid,
            'original_name' =>  $original_name,
            'unique_name'   =>  $unique_name
        );

        if (is_numeric($reply_id)) {
            $fields['reply_id'] = $reply_id;
        }

        return $this->db->resultInsertId('ticket_attachments', $fields);
    }

    // Turn the _FILES array into something that's better to work with.
    private function normalizeFiles($files)
    {
        $_files       = array();
        $_files_count = count($files['name']);
        $_files_keys  = array_keys($files);

        for ($i = 0; $i < $_files_count; $i++) {
            if (empty($files['tmp_name'][$i])) {
                continue;
            }

            foreach ($_files_keys as $key) {
                $_files[$i][$key] = $files[$key][$i];
            }
        }

        return array_values($_files);
    }

    public function checkPath()
    {
        if (empty($this->filesArray)) {
            return null;
        }

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }

        return is_writable($this->path);
    }

    // Create an array of mimetypes based on the allowed extensions.
    private function createMimeArray()
    {
        $permittedMimeTypes = [];
        foreach ($this->allMimeTypes['mimes'] as $ext => $mimes) {
            if (in_array($ext, $this->permittedExtensions)) {
                $permittedMimeTypes = array_merge($permittedMimeTypes, $mimes);
            }
        }

        $this->permittedMimeTypes = $permittedMimeTypes;
    }

    private function checkSize($uploadedFileSize)
    {
        return ($uploadedFileSize > $this->maxSize);
    }

    // Don't rely on $_FILES type which can be spoofed. Get the true mimetype via finfo.
    private function getMimeType($file)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file);
        return $mime;
    }
}