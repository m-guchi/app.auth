<?php

include_once(__DIR__."/../db.php");
$db = new DB();

// var_dump($_POST);

if(!empty($_POST)){
    switch($_POST["type"]){
        case "page":

            $page_id = htmlspecialchars($_POST["page_id"]);

            if($_POST["method"]=="edit"){

                if($_POST["submit"]=="削除"){
                    try{
                        $sql = "DELETE FROM pages WHERE id = :id";
                        $sth = $db->pdo->prepare($sql);
                        $sth->bindValue(':id', $page_id);
                        $sth->execute();
                    }catch(PDOException $e){
                        echo $e;
                        exit();
                    }

                }else if($_POST["submit"]=="登録"){
                    $title_name = htmlspecialchars($_POST["title"]);
                    $overview = htmlspecialchars($_POST["overview"]);
                    $url = htmlspecialchars($_POST["url"]);
                    $anyone = $_POST["anyone"];
                    try{
                        $sql = "UPDATE pages SET title_name = :title_name, overview = :overview, url = :url, anyone_access = :anyone_access, anyone_display = :anyone_display WHERE id = :id";
                        $sth = $db->pdo->prepare($sql);
                        $sth->bindValue(':id', $page_id);
                        $sth->bindValue(':title_name', $title_name);
                        $sth->bindValue(':overview', $overview);
                        $sth->bindValue(':url', $url);
                        $sth->bindValue(':anyone_access', in_array("access",$anyone)?1:0);
                        $sth->bindValue(':anyone_display', in_array("display",$anyone)?1:0);
                        $sth->execute();
                    }catch(PDOException $e){
                        echo $e;
                        exit();
                    }
                }
            }else if($_POST["method"]=="add"){
                $title_name = htmlspecialchars($_POST["title"]);
                $overview = htmlspecialchars($_POST["overview"]);
                $url = htmlspecialchars($_POST["url"]);
                $anyone = $_POST["anyone"];
                try{
                    $sql = "INSERT INTO pages (id, title_name, overview, url, anyone_access, anyone_display) VALUES (:id, :title_name, :overview, :url, :anyone_access, :anyone_display)";
                    $sth = $db->pdo->prepare($sql);
                    $sth->bindValue(':id', $page_id);
                    $sth->bindValue(':title_name', $title_name);
                    $sth->bindValue(':overview', $overview);
                    $sth->bindValue(':url', $url);
                    $sth->bindValue(':anyone_access', in_array("access",$anyone)?1:0);
                    $sth->bindValue(':anyone_display', in_array("display",$anyone)?1:0);
                    $sth->execute();
                }catch(PDOException $e){
                    echo $e;
                    exit();
                }
            }

            break;

        case "user":

            $user_id = htmlspecialchars($_POST["user_id"]);

            if($_POST["method"]=="edit"){

                if($_POST["submit"]=="削除"){
                    try{
                        $sql = "DELETE FROM users WHERE id = :id";
                        $sth = $db->pdo->prepare($sql);
                        $sth->bindValue(':id', $user_id);
                        $sth->execute();
                    }catch(PDOException $e){
                        echo $e;
                        exit();
                    }

                }else if($_POST["submit"]=="登録"){
                    $user_name = htmlspecialchars($_POST["name"]);
                    if(empty($_POST["password"])){
                        try{
                            $sql = "UPDATE users SET user_name = :user_name WHERE id = :id]";
                            $sth = $db->pdo->prepare($sql);
                            $sth->bindValue(':id', $user_id);
                            $sth->bindValue(':user_name', $user_name);
                            $sth->execute();
                        }catch(PDOException $e){
                            echo $e;
                            exit();
                        }
                    }else{
                        $password = password_hash($_POST["password"],\PASSWORD_DEFAULT);
                        try{
                            $sql = "UPDATE users SET user_name = :user_name, password = :password WHERE id = :id";
                            $sth = $db->pdo->prepare($sql);
                            $sth->bindValue(':id', $user_id);
                            $sth->bindValue(':user_name', $user_name);
                            $sth->bindValue(':password', $password);
                            $sth->execute();
                        }catch(PDOException $e){
                            echo $e;
                            exit();
                        }
                    }
                }
            }else if($_POST["method"]=="add"){
                $user_name = htmlspecialchars($_POST["name"]);
                $password = password_hash($_POST["password"],\PASSWORD_DEFAULT);
                try{
                    $sql = "INSERT INTO users (id, user_name, password) VALUES (:id, :user_name, :password)";
                    $sth = $db->pdo->prepare($sql);
                    $sth->bindValue(':id', $user_id);
                    $sth->bindValue(':user_name', $user_name);
                    $sth->bindValue(':password', $password);
                    $sth->execute();
                }catch(PDOException $e){
                    echo $e;
                    exit();
                }
            }

            break;

        case "scope":

            try{
                $sql = "DELETE FROM scopes";
                $sth = $db->pdo->prepare($sql);
                $sth->execute();
            }catch(PDOException $e){
                echo $e;
                exit();
            }

            foreach($_POST["scope"] as $user_id => $page_data){
                foreach($page_data as $page_id => $scope){
                    $access = in_array("access",$scope)?1:0;
                    $display = in_array("display",$scope)?1:0;
                    try{
                        $sql = "INSERT INTO scopes (page_id, user_id, access, display) VALUES (:page_id, :user_id, :access, :display)";
                        $sth = $db->pdo->prepare($sql);
                        $sth->bindValue(':user_id', $user_id);
                        $sth->bindValue(':page_id', $page_id);
                        $sth->bindValue(':access', $access);
                        $sth->bindValue(':display', $display);
                        $sth->execute();
                    }catch(PDOException $e){
                        echo $e;
                        exit();
                    }
                }
            }

            break;
    }
}


try{
    $sql = "SELECT * FROM users";
    $sth = $db->pdo->prepare($sql);
    $sth->execute();
}catch(PDOException $e){
    echo $e;
    exit();
}
$user_data = $sth->fetchAll(PDO::FETCH_ASSOC);

try{
    $sql = "SELECT * FROM pages";
    $sth = $db->pdo->prepare($sql);
    $sth->execute();
}catch(PDOException $e){
    echo $e;
    exit();
}
$page_data = $sth->fetchAll(PDO::FETCH_ASSOC);

try{
    $sql = "SELECT * FROM scopes";
    $sth = $db->pdo->prepare($sql);
    $sth->execute();
}catch(PDOException $e){
    echo $e;
    exit();
}
$scope_data = $sth->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appアクセス制限管理</title>
    <meta name="description" content="Appのアクセス制限を管理します" />
    <meta name="format-detection" content="telephone=no" />
    <!-- <link rel="apple-touch-icon" type="image/png" href="apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="icon-192x192.png">
    <link rel="manifest" href="manifest.json"> -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <a href="https://app.minagu.work">TOP</a>
    <div id="container">
        <div id="item-page">
            <h2>ページ</h2>
            <table>
                <tr>
                    <th rowspan="2">ID</th>
                    <th rowspan="2">タイトル</th>
                    <th rowspan="2">概要</th>
                    <th rowspan="2">URL</th>
                    <th colspan="2">全ユーザー</th>
                    <th rowspan="2">編集</th>
                </tr>
                <tr>
                    <th>表示</th>
                    <th>アクセス</th>
                </tr>
<?php foreach($page_data as $page){ ?>
                <tr id="<?=$page["id"]?>">
                    <form action="" method="post">
                        <td><?=$page["id"]?></td>
                        <td>
                            <span class="noedit"><?=$page["title_name"]?></span>
                            <textarea class="edit" name="title" style="width:90%; height:8em;" required><?=$page["title_name"]?></textarea>
                        </td>
                        <td>
                            <span class="noedit"><?=$page["overview"]?></span>
                            <textarea class="edit" name="overview" style="width:90%; height:8em;"><?=$page["overview"]?></textarea>
                        </td>
                        <td class="url">
                            <a class="noedit" target="_blank" href="<?=$page["url"]?>"><?=$page["url"]?></a>
                            <textarea class="edit" name="url" style="width:90%; height:8em;"><?=$page["url"]?></textarea>
                        </td>
                        <td>
                            <input class="noedit" type="checkbox" onclick="return false;" <?=$page["anyone_display"]?"checked":""?> />
                            <input class="edit" type="checkbox" name="anyone[]" value="display" <?=$page["anyone_display"]?"checked":""?> />
                        </td>
                        <td>
                            <input class="noedit" type="checkbox" onclick="return false;" <?=$page["anyone_access"]?"checked":""?> />
                            <input class="edit" type="checkbox" name="anyone[]" value="access" <?=$page["anyone_access"]?"checked":""?> />
                        </td>
                        <td>
                            <input type="hidden" name="page_id" value="<?=$page["id"]?>">
                            <input type="hidden" name="type" value="page">
                            <input type="hidden" name="method" value="edit">
                            <input class="noedit" type="button" value="編集" onClick="editPage('<?=$page["id"]?>')">
                            <input class="edit" type="submit" name="submit" value="登録">
                            <input class="edit" type="submit" name="submit" value="削除" onClick="if(!confirm('削除します')){return false;}">
                        </td>
                    </form>
                </tr>
<?php } ?>
                <tr id="add_page_form" style="display: none;">
                    <form action="" method="post">
                        <td>
                            <textarea name="page_id" style="width:90%; height:8em;" required></textarea>
                        </td>
                        <td>
                            <textarea name="title" style="width:90%; height:8em;" required></textarea>
                        </td>
                        <td>
                            <textarea name="overview" style="width:90%; height:8em;"></textarea>
                        </td>
                        <td class="url">
                            <textarea name="url" style="width:90%; height:8em;"></textarea>
                        </td>
                        <td>
                            <input type="checkbox" name="anyone[]" value="display" />
                        </td>
                        <td>
                            <input type="checkbox" name="anyone[]" value="access"/>
                        </td>
                        <td>
                            <input type="hidden" name="type" value="page">
                            <input type="hidden" name="method" value="add">
                            <input type="submit" name="submit" value="登録">
                        </td>
                    </form>
                </tr>
            </table>
            <input type="button" value="追加" onClick="addPage()">
        </div>
        <div id="item-user">
            <h2>ユーザー</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>ユーザー名</th>
                    <th>パスワード</th>
                    <th>編集</th>
                </tr>
<?php foreach($user_data as $user){ ?>
                <tr id="<?=$user["id"]?>">
                    <form action="" method="post">
                        <td><?=$user["id"]?></td>
                        <td>
                            <span class="noedit"><?=$user["user_name"]?></span>
                            <input class="edit" type="text" name="name" style="width:90%;" value="<?=$user["user_name"]?>" required>
                        </td>
                        <td>
                            <span class="noedit">*****<?=substr($user["password"], -3)?></span>
                            <input class="edit" type="password" name="password" style="width:90%;" value="" placeholder="パスワードを変更しない場合は入力しない">
                        </td>
                        <td>
                            <input type="hidden" name="user_id" value="<?=$user["id"]?>">
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="method" value="edit">
                            <input class="noedit" type="button" value="編集" onClick="editUser('<?=$user["id"]?>')">
                            <input class="edit" type="submit" name="submit" value="登録">
                            <input class="edit" type="submit" name="submit" value="削除" onClick="if(!confirm('削除します')){return false;}">
                        </td>
                    </form>
                </tr>
<?php } ?>
                <tr id="add_user_form" style="display: none;">
                    <form action="" method="post">
                        <td>
                            <input type="text" name="user_id" style="width:90%;" value="" required>
                        </td>
                        <td>
                            <input type="text" name="name" style="width:90%;" value="" required>
                        </td>
                        <td>
                            <input type="password" name="password" style="width:90%;" value="" required>
                        </td>
                        <td>
                            <input type="hidden" name="type" value="user">
                            <input type="hidden" name="method" value="add">
                            <input type="submit" name="submit" value="登録">
                        </td>
                    </form>
                </tr>
            </table>
            <input type="button" value="追加" onClick="addUser()">
        </div>
        <div id="item-scope">
            <h2>ページ権限</h2>
            <div style="text-align: right;">表示 / アクセス</div>
            <form action="" method="post">
            <table>
                <tr>
                    <th>page＼user</th>
                    <th>全ユーザー</th>
<?php foreach($user_data as $user){ ?>
                    <th><?=$user["id"]?></th>
<?php } ?>
                </tr>
<?php
    foreach($page_data as $page){
?>
                <tr>
                    <th><?=$page["id"]?></th>
                    <th>
                        <input type="checkbox" disabled <?=$page["anyone_display"]?"checked":""?> /> / 
                        <input type="checkbox" disabled <?=$page["anyone_access"]?"checked":""?>/>
                    </th>
<?php
    foreach($user_data as $user){
        $target_scope = array_merge(array_filter($scope_data, function($val) use ($page, $user){
            return $val["page_id"]==$page["id"] && $val["user_id"]==$user["id"];
        }));
        if(count($target_scope)>2){ echo "scope table が適切に設定されていない可能性があります。"; }
        $display = count($target_scope)==0 ? false : $target_scope[0]["display"];
        $access = count($target_scope)==0 ? false : $target_scope[0]["access"];
?>
                    <th>
                        <input type="checkbox" name="scope[<?=$user["id"]?>][<?=$page["id"]?>][]" value="display" <?=$display?"checked":""?> /> / 
                        <input type="checkbox" name="scope[<?=$user["id"]?>][<?=$page["id"]?>][]" value="access" <?=$access?"checked":""?> />
                    </th>
<?php } ?>
                </tr>
<?php } ?>
            </table>
            <input type="hidden" name="type" value="scope">
            <input type="hidden" name="method" value="edit">
            <input type="submit" value="登録">
            </form>
        </div>
    </div>
    <script type="text/javascript">
        function editPage(page){
            const pageEl = document.getElementById(page);
            const editEl = pageEl.getElementsByClassName("edit")
            Array.prototype.forEach.call(editEl, el => {el.style.display="block"});
            const noeditEl = pageEl.getElementsByClassName("noedit")
            Array.prototype.forEach.call(noeditEl, el => {el.style.display="none"});
        }
        function editUser(user){
            const userEl = document.getElementById(user);
            const editEl = userEl.getElementsByClassName("edit")
            Array.prototype.forEach.call(editEl, el => {el.style.display="block"});
            const noeditEl = userEl.getElementsByClassName("noedit")
            Array.prototype.forEach.call(noeditEl, el => {el.style.display="none"});
        }
        function addPage(){
            const addPageEl = document.getElementById("add_page_form");
            addPageEl.style.display="contents"
        }
        function addUser(){
            const addUserEl = document.getElementById("add_user_form");
            addUserEl.style.display="contents"
        }
    </script>
</body>