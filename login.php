<?php
// エラー表示
ini_set("display_errors", 1);
require_once('func.php');
session_start();
$pdo = db_conn();

// ログイン処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lid = $_POST["lid"];
    $lpw = $_POST["lpw"];

    // 1. データ登録SQL作成
    $stmt = $pdo->prepare("SELECT * FROM users WHERE lid=:lid");
    $stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
    $status = $stmt->execute();

    // 2. SQL実行時にエラーがある場合STOP
    if ($status == false) {
        sql_error($stmt);
    }

    // 3. 抽出データ数を取得
    $val = $stmt->fetch();

    // 4. 該当1レコードがあればSESSIONに値を代入
    $pw = password_verify($lpw, $val["lpw"]);
    if ($pw) {
        // Login成功時
        $_SESSION["chk_ssid"] = session_id();
        $_SESSION["kanri_flg"] = $val['kanri_flg'];
        $_SESSION["name"] = $val['name'];
        // Login成功時（main.phpへリダイレクト）
        header("Location: main.php");
        exit();
    } else {
        // Login失敗時（login.phpへリダイレクト）
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 300px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <h1>ログイン</h1>
    <form method="post" action="">
        <label for="lid">ID:</label>
        <input type="text" id="lid" name="lid" required>
        <label for="lpw">Password:</label>
        <input type="password" id="lpw" name="lpw" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>