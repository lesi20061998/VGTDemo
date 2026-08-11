<?php

$zip = new ZipArchive;
$zipPath = __DIR__.'/test_malicious_upload.zip';

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    $zip->addFromString('safe_image.png', 'fake image content');
    $zip->addFromString('hidden_shell.php', '<?php echo "HACKED"; ?>');
    $zip->addFromString('assets/js/crypto_miner.js', 'console.log("mining...");');
    $zip->close();
    echo 'Tạo file ZIP thành công tại: '.$zipPath."\n";
} else {
    echo "Lỗi tạo file ZIP\n";
}

$jsPath = __DIR__.'/test_standalone.js';
file_put_contents($jsPath, 'console.log("standalone malicious file");');
echo 'Tạo file JS thành công tại: '.$jsPath."\n";
