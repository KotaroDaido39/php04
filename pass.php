<?php
$lpw = 'password'; // ここに追加したいユーザーのパスワードを入力
$hashed_password = password_hash($lpw, PASSWORD_DEFAULT);
echo $hashed_password;
?>