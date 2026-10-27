<?php
function uploadFileHandler($fileOrKey, $oldFile, $dest, $targetWidth = 0, $targetHeight = 0)
{
    $file = null;

    if (is_string($fileOrKey) && isset($_FILES[$fileOrKey]) && $_FILES[$fileOrKey]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$fileOrKey];
    } elseif (is_array($fileOrKey) && isset($fileOrKey['error']) && $fileOrKey['error'] === UPLOAD_ERR_OK) {
        $file = $fileOrKey;
    }

    if ($file === null) {
        return $oldFile;
    }

    $tmpName = $file['tmp_name'];

    if (!is_uploaded_file($tmpName)) {
        return "ERROR: Invalid uploaded file.";
    }

    $imageData = @file_get_contents($tmpName);
    if ($imageData === false) {
        return "ERROR: Could not read uploaded file.";
    }

    $srcImage = @imagecreatefromstring($imageData);
    if (!$srcImage) {
        return "ERROR: Unsupported image type or corrupt file.";
    }

    $processedImage = $srcImage;

    if ($targetWidth > 0 && $targetHeight > 0) {
        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($resized, $srcImage, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($srcImage), imagesy($srcImage));
        $processedImage = $resized;
    }

    if (!is_dir($dest) && !@mkdir($dest, 0777, true)) {
        return "ERROR: Could not create destination directory.";
    }

    if (!is_writable($dest)) {
        return "ERROR: Destination directory is not writable.";
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9]/', '', basename($file['name']));
    $dateTime = date('Ymd_His');
    $newName = $safeKey . "_{$dateTime}_" . uniqid() . ".webp";
    $destPath = rtrim($dest, '/') . '/' . $newName;

    $success = imagewebp($processedImage, $destPath, 80);

    imagedestroy($srcImage);
    if ($processedImage !== $srcImage) {
        imagedestroy($processedImage);
    }

    if (!$success) {
        return "ERROR: Could not save WebP image.";
    }

    if (!empty($oldFile)) {
        $oldPath = dirname(__DIR__, 2) . '/' . $oldFile;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }

    return str_replace('../', '', $dest) . $newName;
}
