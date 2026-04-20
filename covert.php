<?php

try{
    $sourceFile = 'test.png';
    $cdnLink = convertToWebp($sourceFile);
    echo $cdnLink;
}catch (\Exception $e){
    echo $e->getMessage();
}

function convertToWebp($filename, $quality = 85) {
    $currentDir = dirname( __FILE__) . DIRECTORY_SEPARATOR;
    $src = $currentDir . $filename;
    if (!file_exists($src)) {
        throw new Exception('File not found: ' . $src);
    }
    $dstName = str_replace(['jpg','png','jpeg'],'webp',$filename);
    $dst = $currentDir . $dstName;
    $info = getimagesize($src);

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($src);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($src);
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    } else {
        throw new  \Exception('转换失败:'.$info['mime']);
    }
    imagewebp($image, $dst, $quality);
    imagedestroy($image);
    unlink($src);
    return 'https://cdn.jsdelivr.net/gh/Jonlincy/imgsrc@main/' . $dstName;
}