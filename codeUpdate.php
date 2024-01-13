<?php
// 获取当前目录下的所有.php文件
$files = glob("*.php");

// 读取文件vip1.php的内容
$file1_lines = file('vip1.php');

// 从第四行开始截取
$file1_content = array_slice($file1_lines, 3);
$file1_content = implode("", $file1_content); // 转换为字符串

foreach ($files as $file) {
    // 跳过1.php，vip1.php和admin.php文件
    if ($file == 'codeUpdate.php' || $file == 'vip1.php' || $file == 'admin.php' || $file == 'deleteFile.php' || $file == 'processForm.php') {
        continue;
    }

    // 读取目标文件的内容
    $file_lines = file($file);

    // 截取前3行
    $file_content_before = array_slice($file_lines, 0, 3);

    // 转换为字符串
    $file_content_before_string = implode("", $file_content_before);

    // 合并文件的前三行、换行符和文件1.php的内容
    $file_final_content = $file_content_before_string . $file1_content;

    // 将新的内容写入目标文件
    file_put_contents($file, $file_final_content);
}

// 操作完成后返回到原页面，并附带一个成功的消息
header("Location: admin.php?view=vip_editor&batch_issue=success");
?>
