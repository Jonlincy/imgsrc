<?php

try{
    $sourceFile = $argv[1] ?? '';
    if (empty($sourceFile)){
        die('命令为: php ./covert.php xxx.png');
    }

   if ($sourceFile === 'png'){
       $pngData = glob("*.png");
       foreach ($pngData as $file) {
           $cdnLink = convertToWebp($file);
           echo $cdnLink . PHP_EOL;
       }
   }else{
       $cdnLink = convertToWebp($sourceFile);
       echo $cdnLink;
   }
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
    $sourceSize = filesize($src);
    $distSize = filesize($dst);
    $rate = round((($sourceSize - $distSize) / $sourceSize) * 100 ,2);
    echo '转换成功:' .$filename . '('.formatBytes($sourceSize).') -> '. $dstName . '('.formatBytes($distSize).'),压缩率约:'.$rate .'%' .PHP_EOL;
    unlink($src);
    return 'https://cdn.jsdelivr.net/gh/Jonlincy/imgsrc@main/' . str_replace('.\\','',$dstName);
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . '' . $units[$pow];
}