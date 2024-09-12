<?php
// エラー表示
ini_set("display_errors", 1);

// 1. DB接続します
include('func.php');
$pdo = db_conn();

// 2. POSTデータ取得
$id = $_POST['id'];

// 3. 現在のブックマーク状態を取得
$sql = "DELETE FROM qiita_table2 WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute(); // 実行

if ($status == false) {
    $error = $stmt->errorInfo();
    echo json_encode(['status' => 'error', 'message' => $error[2]]);
} else {
    echo json_encode(['status' => 'success']);
}
?>


?>