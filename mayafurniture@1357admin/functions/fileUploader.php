<?php

function uploadFileHandler($fileOrKey, $oldFile, $dest, $targetWidth = 0, $targetHeight = 0)
{
    $file = null;
    if (is_string($fileOrKey)) {
        if (isset($_FILES[$fileOrKey]) && $_FILES[$fileOrKey]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$fileOrKey];
        }
    } elseif (is_array($fileOrKey) && isset($fileOrKey['error']) && $fileOrKey['error'] === UPLOAD_ERR_OK) {
        $file = $fileOrKey;
    }

    if ($file === null) {
        return $oldFile;
    }

    $tmpName = $file['tmp_name'];
    $keyIdentifier = is_string($fileOrKey) ? $fileOrKey : 'file';
    $imageData = @file_get_contents($tmpName);
    if ($imageData === false) {
        return "ERROR: Could not read uploaded file.";
    }
    
    $srcImage = @imagecreatefromstring($imageData);
    if (!$srcImage) {
        return "ERROR: Unsupported image type or corrupt file for '$keyIdentifier'.";
    }

    $width = imagesx($srcImage);
    $height = imagesy($srcImage);
    $processedImage = $srcImage;

    if ($targetWidth > 0 && $targetHeight > 0) {
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefilledrectangle($resizedImage, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        $processedImage = $resizedImage;
    }

    if (!is_dir($dest)) {
        if (!mkdir($dest, 0777, true)) {
            return "ERROR: Failed to create destination directory.";
        }
    }

    $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($keyIdentifier));
    $dateTime = date('Ymd_His');
    $newName = $safeKey . '_img_' . $dateTime . '_' . uniqid() . '.webp';
    $destinationPath = rtrim($dest, '/') . '/' . $newName;
    $saveSuccess = imagewebp($processedImage, $destinationPath, 80);

    imagedestroy($srcImage);
    if ($processedImage !== $srcImage) {
        imagedestroy($processedImage);
    }

    if ($saveSuccess) {
        if (!empty($oldFile)) {
             $oldFilePath = realpath(__DIR__ . '/../../' . $oldFile);
            if ($oldFilePath && file_exists($oldFilePath)) {
                 @unlink($oldFilePath);
            }
        }
        return $newName;
    } else {
        return "ERROR: Could not save WebP image for '$keyIdentifier'.";
    }
}

function uploadGenericFileHandler($fileOrKey, $dest, $allowedExtensions = array('pdf'), $oldFile = null)
{
    $file = null;
    if (is_string($fileOrKey)) {
        if (isset($_FILES[$fileOrKey]) && $_FILES[$fileOrKey]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$fileOrKey];
        }
    } elseif (is_array($fileOrKey) && isset($fileOrKey['error']) && $fileOrKey['error'] === UPLOAD_ERR_OK) {
        $file = $fileOrKey;
    }

    if ($file === null) {
        return array('error' => 'No file uploaded or an upload error occurred.');
    }
    
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        return array('error' => 'Invalid file type. Only ' . implode(', ', $allowedExtensions) . ' are allowed.');
    }

    if (!is_dir($dest)) {
        if (!mkdir($dest, 0777, true)) {
            return array('error' => 'Failed to create destination directory.');
        }
    }

    $safeFileName = preg_replace('/[^a-zA-Z0-9_.\-]/', '', basename($fileName));
    $newFileName = time() . '_' . uniqid() . '_' . $safeFileName;
    $destinationPath = rtrim($dest, '/') . '/' . $newFileName;

    if (move_uploaded_file($fileTmpName, $destinationPath)) {
        if ($oldFile) {
            $oldFilePath = realpath(__DIR__ . '/../../' . $oldFile);
            if ($oldFilePath && file_exists($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }
        return array('success' => true, 'fileName' => $newFileName);
    } else {
        return array('error' => 'Failed to move the uploaded file.');
    }
}
?>