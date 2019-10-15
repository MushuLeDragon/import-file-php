<?php

// localhost/import/new-import.php
var_dump($_FILES);

// https://www.php.net/manual/fr/features.file-upload.php#114004
try {

  // Undefined | Multiple Files | $_FILES Corruption Attack
  // If this request falls under any of them, treat it invalid.
  if (
    !isset($_FILES['upfile']['error']) ||
    is_array($_FILES['upfile']['error'])
  ) {
    throw new RuntimeException('Invalid parameters.');
  }

  // Check $_FILES['upfile']['error'] value.
  switch ($_FILES['upfile']['error']) {
    case UPLOAD_ERR_OK:
      break;
    case UPLOAD_ERR_NO_FILE:
      throw new RuntimeException('No file sent.');
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE:
      throw new RuntimeException('Exceeded filesize limit (in php.ini).>>>>>>>>');
    default:
      throw new RuntimeException('Unknown errors.');
  }

  // You should also check filesize here.
  $size = 5000000;
  if ($_FILES['upfile']['size'] > $size) {
    throw new RuntimeException("Exceeded filesize limit (> $size).");
  }

  // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
  // Check MIME Type by yourself.
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  if (false === $ext = array_search(
    $finfo->file($_FILES['upfile']['tmp_name']),
    array(
      'jpg' => 'image/jpeg',
      'png' => 'image/png',
      'gif' => 'image/gif',
    ),
    true
  )) {
    throw new RuntimeException('Invalid file format.');
  }

  // You should name it uniquely.
  // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
  // On this example, obtain safe unique name from its binary data.
  if (!move_uploaded_file(
    $_FILES['upfile']['tmp_name'],
    sprintf(
      './uploads/%s.%s',
      sha1_file($_FILES['upfile']['tmp_name']),
      $ext
    )
  )) {
    throw new RuntimeException('Failed to move uploaded file.');
  }

  echo 'File is uploaded successfully.';
} catch (RuntimeException $e) {

  echo $e->getMessage();
}
