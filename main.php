<?php
// エラー表示
ini_set("display_errors", 1);
require_once('func.php');
$pdo = db_conn();

session_start();
$kanri_flg = $_SESSION['kanri_flg'] ?? 0; // セッションからkanri_flgを取得
$name = $_SESSION['name'] ?? 'ゲスト'; // セッションからユーザー名を取得
// 2. データ取得SQL作成
$sql = "SELECT id, title, author, author_img, url, bookmark, explanation FROM qiita_table2 ORDER BY bookmark DESC, id ASC";
$stmt = $pdo->prepare($sql);
$status = $stmt->execute(); // 実行

// 3. データ表示
if($status==false) {
    // execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit("SQLError:".$error[2]);
}

// 全データ取得
$values = $stmt->fetchAll(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC[カラム名のみで取得できるモード

// HTMLとCSSを追加して表示
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>データ表示</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f8f8f8;
            color: #333;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .bookmark, .delete {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .bookmark:hover, .delete:hover {
            background-color: #0056b3;
        }

        .delete {
            background-color: #dc3545;
        }

        .delete:hover {
            background-color: #c82333;
        }

        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        button[type="submit"] {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .add-article {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .add-article:hover {
            background-color: #0056b3;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #333;
            color: #fff;
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleBookmark(id) {
            $.ajax({
                url: 'update_bookmark.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response == 'success') {
                        location.reload();
                    } else {
                        alert('更新に失敗しました。');
                    }
                }
            });
        }

        function deleteButton(id) {
            if (confirm('本当に削除しますか？')) {
                $.ajax({
                    url: 'delete.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        if (response == 'success') {
                            location.reload();
                            console.log(response);
                        } else {
                            alert('削除出来ました。');
                            location.reload();
                            console.log(response);
                        }
                    }
                });
            }
        }
    </script>
</head>
<body>
    <h1>Qiita お勧め記事</h1>
    <div class="header">
        <div class="username">ログインユーザー: <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <a href="qiita_input.php" class="add-article">記事追加</a>
    <table>
        <tr>
            <th>Title</th>
            <th>作成者</th>
            <th></th>
            <th>URL</th>
            <th>お気に入り</th>
            <th>コメント</th>
        </tr>
        <?php foreach ($values as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row["author"], ENT_QUOTES, 'UTF-8') ?></td>
                <td><img src="<?= htmlspecialchars($row["author_img"], ENT_QUOTES, 'UTF-8') ?>" width="50"></td>
                <td><a href="<?= htmlspecialchars($row["url"], ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars($row["url"], ENT_QUOTES, 'UTF-8') ?></a></td>
                <td>
                    <?php if ($row["bookmark"] == 1): ?>
                        <button class="bookmark" onclick="toggleBookmark(<?= $row['id'] ?>)">★</button>
                    <?php else: ?>
                        <button class="bookmark" onclick="toggleBookmark(<?= $row['id'] ?>)">お気に入り登録</button>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="update_explanation.php" method="post">
                    <textarea name="explanation" rows="2" cols="20"><?= htmlspecialchars($row["explanation"], ENT_QUOTES, "UTF-8") ?></textarea>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">更新</button>
                    </form>
                </td>
                <?php if ($kanri_flg == 1): ?>
                    <td><button onclick="deleteButton(<?= $row['id'] ?>)">削除</button></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>