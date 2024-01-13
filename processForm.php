<?php
session_start();
$baseurl = getenv('baseurl');

if (isset($_POST['submit'])) {
    function generateRandomString($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        // Ensure the first character is a letter
        $randomString .= $characters[rand(0, $charactersLength - 1)];

        for ($i = 1; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    function generateFiles($baseurl, $num_files, $content) {
        $output = '';

        for ($j = 0; $j < $num_files; $j++) {
            $random_string = generateRandomString();

            // 创建同名的PHP和TXT文件
            $php_file = $random_string . '.php';
            $txt_file = $random_string . '.txt';

            // 检查文件是否存在，如不存在则创建
            if (!file_exists($php_file)) {
                touch($php_file);
                $output .= "{$baseurl}{$php_file}\n";
            }

            if (!file_exists($txt_file)) {
                touch($txt_file);
            }

            // 将参数中的内容写入新建的TXT文件
            file_put_contents($txt_file, $content);

            // 检查文件vip1.php是否存在
            if (file_exists("vip1.php")) {
                // 读取vip1.php的内容
                $original_php_content = file_get_contents("vip1.php");

                // 将第三行替换为新TXT文件的文件路径
                $modified_php_content = preg_replace('/(\$file_path = ")([^"]+)(";)/', '$1' . $txt_file . '$3', $original_php_content, 1);

                // 将修改后的内容写入新的PHP文件
                file_put_contents($php_file, $modified_php_content);
            }
        }

        return $output;
    }

    // 调用函数，生成指定数量的文件，并指定写入的内容
    $num_files = intval($_POST['num_files']);
    $content = $_POST['content'];
    $generated_links = generateFiles($baseurl, $num_files, $content);

    // 将生成的卡密链接保存到 session
    $_SESSION['generated_links'] = $generated_links;

    // 重定向回 index.php 页面
    header("Location: admin.php?view=file_generator");
}
?>
