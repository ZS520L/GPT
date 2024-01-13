<?php
session_start();
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

$username = getenv('username');
$password = getenv('password');
$baseurl = getenv('baseurl');


// Login logic
if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($_POST['username'] === $username && $_POST['password'] === $password) {
        $_SESSION['authenticated'] = true;
        // 重定向回 index.php 页面
        header("Location: admin.php");
        exit;
    } else {
        $loginError = '用户名或密码错误，请重新输入！';
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    unset($_SESSION['authenticated']);
}

// File deletion logic
if (isset($_SESSION['authenticated']) && isset($_GET['delete'])) {
    $file = $_GET['delete'];
    $txt_file = $file . '.txt';
    $php_file = $file . '.php';

    if ($file === "vip1") {
        $deleteError = "系统文件，无法删除！";
    } else {
        if (file_exists($txt_file)) {
            unlink($txt_file);
        }

        if (file_exists($php_file)) {
            unlink($php_file);
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}


if (isset($_GET['edit']) && isset($_GET['balance'])) {
    $file = $_GET['edit'];
    $new_balance = $_GET['balance'];

    if (is_numeric($new_balance) && $new_balance >= 0) {
        file_put_contents($file, $new_balance);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $editError = "无效的次数值，请输入一个非负数。";
    }
}


// Pagination logic
$filesPerPage = 8;
$totalFiles = count(glob("*.txt"));
$totalPages = ceil($totalFiles / $filesPerPage);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$startAt = ($page - 1) * $filesPerPage;

$files = array_slice(glob("*.txt"), $startAt, $filesPerPage);

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search_query = $_GET['search'];
    $files = array_filter(glob("*.txt"), function($file) use ($search_query) {
        return strpos($file, $search_query) !== false;
    });
}
$maxPagesToShow = 5; // 可视的分页数，可根据需要调整
$startPage = max(1, $page - floor($maxPagesToShow / 2));
$endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>卡密管理系统</title>
    <link rel="stylesheet" href="change.css">
</head>
<body>
<div class="container">
    <h1 class="mt-4 mb-4" style='text-align:center'>卡密管理系统</h1>

    <?php
    if (isset($_SESSION['authenticated'])) { ?>
        <!--<a href="?logout" class="btn btn-danger">注销</a>-->

        <!-- Menu -->
        <ul class="nav nav-tabs mt-4">
            <li class="nav-item">
                <a class="nav-link <?php if(!isset($_GET['view']) || $_GET['view'] == 'file_manager') { echo 'active'; } ?>" href="?view=file_manager">卡密管理</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(isset($_GET['view']) && $_GET['view'] == 'file_generator') { echo 'active'; } ?>" href="?view=file_generator">卡密批量生成</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if(isset($_GET['view']) && $_GET['view'] == 'vip_editor') { echo 'active'; } ?>" href="?view=vip_editor">功能维护升级</a>
            </li>
        </ul>
        
        <?php
        if (isset($_GET['view']) && $_GET['view'] == 'vip_editor') {
            // 以下是处理 VIP 文件的逻辑
            $file = file('vip1.php');  // 将文件读入数组
        
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $file = $_POST['entire_file'];
                file_put_contents('vip1.php', $file);  // 保存修改的文件
            
                echo '<div class="alert alert-success">文件已成功修改！</div>';
            }
        // 下面是显示编辑器的 HTML
        ?>
        <form method="post" class="my-4">
            <div class="form-group">
                <label for="entire_file">核心代码</label>
                <textarea class="form-control" id="entire_file" name="entire_file" rows="25"><?php if(!is_array($file)){
  $file = (array)$file;
}
echo implode('', $file);
 ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">保存修改</button>
            <a href="codeUpdate.php" class="btn btn-danger" onclick="return confirm('是否进行了充分测试？');">批量下发</a>

        </form>
        <?php if(isset($_GET['batch_issue']) && $_GET['batch_issue'] == 'success'): ?>
            <div class="alert alert-success">批量下发成功！</div>
        <?php endif; ?>

        <?php
        }
            else if(isset($_GET['view']) && $_GET['view'] == 'api_editor') {
        ?>
 


        <?php 
        } 
        
        else if(isset($_GET['view']) && $_GET['view'] == 'file_generator') {
        ?>
        
        <!--<h1 class="text-center mt-5 mb-4">卡密批量生成器</h1>-->
        <div class="row justify-content-center" style='margin-top:30px'>
            <div class="col-md-6">
                <form action="processForm.php" method="post" class="border p-4">
                    <div class="form-group">
                        <label for="num_files">生成卡密数量:</label>
                        <input type="number" id="num_files" name="num_files" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="content">写入次数:</label>
                        <input type="text" id="content" name="content" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">生成卡密</button>
                </form>
                <?php
                if (isset($_SESSION['generated_links'])) {
                    $filename = 'card_codes_' . date('YmdHis') . '.txt';
                    file_put_contents($filename, $_SESSION['generated_links']);

                    echo '<div class="alert alert-success mt-4">';
                    echo nl2br($_SESSION['generated_links']);
                    echo '</div>';

                    // 清除会话中的卡密链接
                    unset($_SESSION['generated_links']);
                ?>
                <!-- 添加隐藏的下载链接并在下载完成后调用 deleteFile.php -->
                <a href="<?php echo $filename; ?>" download="<?php echo $filename; ?>" id="download-link" hidden></a>

                <!-- 添加 JavaScript 代码实现自动下载 -->
                <script>
                    document.getElementById("download-link").addEventListener('click', function() {
                        // 点击后等待一段时间以确保文件已下载
                        setTimeout(deleteFile, 2000);
                    });
                    
                    function deleteFile() {
                        var xhr = new XMLHttpRequest();
                        xhr.open("GET", "deleteFile.php?filename=<?= urlencode($filename); ?>", true);
                        xhr.send();
                    }
                    
                    document.getElementById("download-link").click();

                </script>
                <?php
                }
                ?>
            </div>
        </div>

        <?php 
        } 
        else { // default to view=file_manager
        ?>
        <?php if (isset($deleteError)): ?>
            <div class="alert alert-danger mt-4"><?= htmlspecialchars($deleteError); ?></div>
        <?php endif; ?>
        <form action="" method="get" class="form-inline my-2">
            <input class="form-control mr-sm-2" type="text" name="search" placeholder="输入卡密进行搜索..." value="">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">搜索</button>
        </form>

        <?php if (isset($_GET['search'])): ?>
            <p>显示与"<?php echo $_GET['search']; ?>"相关的结果。</p>
        <?php endif; ?>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>文件名</th>
                    <th>接口</th>
                    <th>余额</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($files as $file) {
                    $number = intval(file_get_contents($file));
                    $creationTime = date ("F d Y H:i:s", filemtime($file));
                    echo '<tr>';
                    echo '<td>' . $file . '</td>';
                    echo '<td>' . $baseurl . pathinfo($file)['filename'] . '.php' . '</td>';
                    echo '<td>' . $number . '</td>';
                    echo '<td>' . $creationTime . '</td>';
                    echo '<td><button onclick="editFile(\'' . $file . '\')" class="btn btn-primary">编辑</button> | <a href="?delete=' . pathinfo($file)['filename'] . '" class="btn btn-danger" onclick="return confirm(\'确定要删除这个文件吗？\')">删除</a></td>';
                    echo '</tr>';
                }
                ?>
                <script>
                    function editFile(file) {
                        var newBalance = prompt("请输入新的次数：");
                        if (newBalance !== null && !isNaN(newBalance)) {
                            location.href = "?edit=" + file + "&balance=" + newBalance;
                        } else {
                            alert("输入无效，请输入正确的数字。");
                        }
                    }
                </script>

            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
        
                <!-- 第一页和上一页按钮 -->
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?view=file_manager&page=1">首 页</a></li>
                    <li class="page-item"><a class="page-link" href="?view=file_manager&page=<?= $page - 1 ?>">上一页</a></li>
                <?php endif; ?>
        
                <!-- 如果第一页不在可视范围内，添加一个省略号 -->
                <?php if ($startPage > 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
        
                <!-- 中间的分页数字 -->
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item"><a class="page-link" href="?view=file_manager&page=<?= $i ?>"><?= $i ?></a></li>
                <?php endfor; ?>
        
                <!-- 如果最后一页不在可视范围内，添加一个省略号 -->
                <?php if ($endPage < $totalPages): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
        
                <!-- 下一页和最后一页按钮 -->
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?view=file_manager&page=<?= $page + 1 ?>">下一页</a></li>
                    <li class="page-item"><a class="page-link" href="?view=file_manager&page=<?= $totalPages ?>">末 页</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php 
        } // End else
    } 

    else { // Not authenticated
        ?>

        <h2 class="mt-4">登录</h2>

        <form method="post">
            <?php if (isset($loginError)) { ?>
                <p class="alert alert-danger"><?php echo htmlspecialchars($loginError); ?></p>
            <?php } ?>
            <div class="form-group">
                <label for="username">用户名:</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">密码:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">登录</button>
        </form>

        <?php
    }
    ?>

</div>
</body>
</html>

