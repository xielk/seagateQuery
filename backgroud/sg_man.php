<?php
// 数据库连接参数
$host = '192.168.35.29';
$dbname = 'seagate_mini';
$username = 'seagate_mini';
$password = 'Seagate^&*265';

// 创建数据库连接
function createConnection() {
    global $host, $dbname, $username, $password;
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

// 处理GET请求 - 导出表
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['table_name'])) {
    $table_name = $_GET['table_name'];
    try {
        $pdo = createConnection();
        
        // 获取表结构
        $stmt = $pdo->prepare("SHOW CREATE TABLE `$table_name`");
        $stmt->execute();
        $createTable = $stmt->fetchColumn(1);
        
        // 获取表数据
        $stmt = $pdo->prepare("SELECT * FROM `$table_name`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 生成SQL文件内容
        $sql_content = "DROP TABLE IF EXISTS `$table_name`;\n\n";
        $sql_content .= "$createTable;\n\n";
        
        if (!empty($rows)) {
            $sql_content .= "INSERT INTO `$table_name` VALUES\n";
            $values = [];
            foreach ($rows as $row) {
                $values[] = "(" . implode(", ", array_map(function($value) use ($pdo) {
                    return $value === null ? 'NULL' : $pdo->quote($value);
                }, $row)) . ")";
            }
            $sql_content .= implode(",\n", $values) . ";\n";
        }
        
        // 设置响应头，使浏览器下载文件
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $table_name . '_export.sql"');
        header('Content-Length: ' . strlen($sql_content));
        
        echo $sql_content;
        exit;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode([
            "code" => 1,
            "msg" => "Database error: " . $e->getMessage()
        ]);
        exit;
    }
}

// 处理POST请求 - 上传并执行SQL文件
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 检查是否有文件上传
    if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['sql_file']['tmp_name'];
        $sql_content = file_get_contents($tmp_name);

        try {
            $pdo = createConnection();

            // 开启事务
            $pdo->beginTransaction();
            
            // 执行SQL语句，获取影响的数据条数
            $affectedRows = $pdo->exec($sql_content);

            // 提交事务
            $pdo->commit();

            $response = [
                "code" => 0,
                "msg" => "SQL file uploaded and executed successfully",
                "affected_rows" => $affectedRows
            ];
        } catch (PDOException $e) {
            // 回滚事务
            $pdo->rollBack();
            
            $response = [
                "code" => 1,
                "msg" => "Database error: " . $e->getMessage()
            ];
        }
    } else {
        $response = [
            "code" => 1,
            "msg" => "No SQL file uploaded or upload error occurred"
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// 如果既不是GET也不是POST请求，返回错误信息
header('Content-Type: application/json');
echo json_encode([
    "code" => 1,
    "msg" => "Invalid request method"
]);
?>
