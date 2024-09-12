<?php

require_once('func.php');
$pdo = db_conn();

$id = $_POST['id'];
$explanation = $_POST['explanation'];

$sql = "UPDATE qiita_table2 SET explanation = :explanation WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':explanation', $explanation, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if($status==false){
    sql_error($stmt);
}

redirect('main.php');

?>
