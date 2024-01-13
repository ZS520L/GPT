<?php

$file_path = "vip1.txt";//剩余次数保存在这里

$http_code = 0;
$baseurl = getenv('baseurl');
$api_url = getenv('api_url');
$api_key = getenv('api_key');
// ignore_user_abort(true);

function chat($data,$model){
    global $http_code,$file_path;
    
    $headers = [
        "Authorization: Bearer " . $api_key,
        "Content-Type: application/json",
    ];
    $reqdata = [
        'model' => $model,
        'messages' => $data['messages'],
        'stream' => true
    ];
    
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($reqdata));
    
    header('Content-Type: text/event-stream; charset=utf-8'); // 增加响应头，表示返回类型为 text
    header('X-Accel-Buffering: no');
    
    // 2. 打开输出流处理响应结果
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($curl, $data) {
        echo $data;
        ob_flush();
        flush();
        return strlen($data);
    });
    
    $response = curl_exec($ch);
    
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $http_code;
}

function decreaseNumber($fileName, $decrement) {
    $file_path = $fileName;

    // 读取 txt 文件，返回数字
    $number = file_get_contents($file_path);
    if ($number !== false) {
        $number = intval($number); //将得到的字符串转换为整型
        if ($number >= $decrement) { 
            $number -= $decrement; // 减少对应的次数
            // 写回减少后的数字
            if (file_put_contents($file_path, $number)) {
                return "Success: The number has been decreased by {$decrement}.";
            } else {
                return "Error: Cannot write the updated number to the file.";
            }
        } else {
            return "Error: Not enough counts left.";
        }
    } else {
        return "Error: Cannot read the file.";
    }
}


//标准化消息
function standerdize($text){
    return 'data: {"id":"chatcmpl-mJCgVYn7BNtc","object":"chat.completion.chunk","choices":[{"index":0,"delta":{"role":"assistant","content":' . json_encode($text) . '}}],"finish_reason":null}' . PHP_EOL . PHP_EOL;
}

// 获取请求类型
$request_method = $_SERVER['REQUEST_METHOD'];
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");


if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    exit();
}
// 检查请求路径中是否包含 '/v1/models'，并且请求方法是否为 'GET'
if (strpos($_SERVER['REQUEST_URI'], '/v1/models') !== false && $_SERVER['REQUEST_METHOD'] == 'GET') {
    // 构造响应数据
    $models = [
        "data" => [
        	[
            	"id" => "gpt-3.5-turbo",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-3.5-turbo-16k",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-4",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-4-1106-preview",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-4-32k",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-4-all",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "gpt-4-dalle",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	],
        	[
            	"id" => "free-gpt4",
            	"object" => "model",
            	"owned_by" => "reversed",
            	"tokens" => 8192,
        	]
    	]
    ];

    // 设置响应头
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    
    // 发送JSON响应
    echo json_encode($models);
    exit();
}

$number = file_get_contents($file_path);  // 读取 txt 文件，返回数字
if ($request_method == 'GET') {
    if ($number !== false) {
        $url6 = str_replace('.txt', '.php', $file_path);
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>接口剩余次数</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <style>
    body { 
        font-family: 'Roboto', Arial, sans-serif; 
        margin: 0; 
        padding: 0; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        height: 100vh; 
        background: linear-gradient(90deg, #091236, #010512, #091236); 
    }
    .container { 
        background-color: #1e213a; 
        padding: 20px; 
        border-radius: 8px;
        box-shadow: 0px 0px 8px 0px #4361ee;
        text-align: center; 
        max-width: 90%;
        transition: all 0.3s ease-in-out;
    }
    .container:hover {
        box-shadow: 0px 5px 15px 0px #7190e7;
    }
    h1 {
        font-size: 32px;
        color: #4361ee;
    }
    p {
        color: #adb5bd;
    }
    a {
        display: inline-block;
        margin: 1rem;
        padding: 0.75rem;
        font-size: 20px;
        color: #4361ee;
        background-color: #010512;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease-in-out;
    }
    a:hover {
        color: #7190e7;
        background-color: #12161f;
    }
    @media (min-width: 600px) { 
        .container { 
            max-width: 600px; 
        } 
    }
    </style>
</head>
<body>
    <div class="container">
        <h1>尊敬的客户您好！</h1>
        <p>您的接口剩余调用次数为：{$number}</p>
        <a href="https://ddvebobqmdpw.cloud.sealos.io/#/?settings=%7B%22key%22:%22sk-666%22,%22url%22:%22{$baseurl}{$url6}%22%7D
" target="_blank">大神直接用</a>
<a href="https://api.zhtec.xyz/chat/GPTs.php?settings=%7B%22key%22:%22sk-666%22,%22url%22:%22{$baseurl}{$url6}%22%7D" target="_blank">GPTs</a>
        <a href="https://www.bilibili.com/video/BV1DH4y1k7Bn/?share_source=copy_web&vd_source=4500215f4296928da959d42ffbccf6a7" target="_blank">小白看教程</a>
    </div>
</body>
</html>
HTML;

    echo $html;
}


else {
        http_response_code(500);
        echo "Error: Cannot read the file.";
    }
// 处理 POST 请求
} elseif ($request_method == 'POST') {
    if (intval($number) >0){
        // 1. 获取POST请求的JSON数据
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);// 解码JSON数据，返回数组
        $pattern = '/\([^)]+\)/';
        $data['model'] = preg_replace($pattern, '', $data['model']);
        
        
        header('Content-Type: text/event-stream; charset=utf-8'); // 增加响应头，表示返回类型为 text
        
        $model = $data['model'];
        $http_code = null;
        $decrease_count = 0;
        
        if(strpos($model, 'gpt-4-gizmo') !== false) {
            $http_code = chat($data, $model);
            $decrease_count = 3;
        }else{
            switch ($model) {
                case 'gpt-3.5-turbo':
                case 'gpt-3.5-turbo-16k':
                case 'gpt-3.5-turbo-1106':
                    $http_code = chat($data, $model);
                    $decrease_count = (rand(1, 10) === 6) ? 1 : 0;
                    break;
            
                case 'gpt-4':
                case 'gpt-4-1106-preview':
                case 'mistral-medium':
                case 'qwen-72b':
                    $http_code = chat($data,$model);
                    $decrease_count = 1;
                    break;
      
                case 'free-gpt4':
                    $http_code = chat($data,$model);
                    break;
      
                case 'claude-2':
                case 'stable-diffusion':
                case 'gemini-pro-vision':
                    $http_code = chat($data, $model);
                    $decrease_count = 1;
                    break;
            
                case 'gpt-4-32k':
                case 'net-gpt-4':
                case 'gpt-4-dalle':
                case 'gpt-4-all':
                    $http_code = chat($data, $model);
                    $decrease_count = 3;
                    break;
                
                case 'gpt-4-vision-preview':
                    $http_code = chat($data, 'gpt-4-v');
                    $decrease_count = 3;
                    break;

                case 'gpt-4-classic':
                    $http_code = chat($data, 'gpt-4-gizmo-g-YyyyMT9XH');
                    $decrease_count = 3;
                    break;
       
                case 'mj':
                    $http_code = chat($data, 'mj');
                    $decrease_count = 6;
                    break;
            
                default:
                    echo standerdize('不受支持的模型: ' . $model);
                    echo 'data: [DONE]' . PHP_EOL . PHP_EOL;
                    return;
            }    
        }
        if ($http_code !== 200) {
            echo '接口异常！请稍后重试！';
        } else {
            decreaseNumber($file_path, $decrease_count);
        }
    } else {
        echo '接口调用次数用光啦！请联系管理员充值！';
    }
}
?>

