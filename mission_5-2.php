 <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-2</title>
</head>
<body>  
  
    <?php   
    
        //データベースに接続
       $dsn = 'データベース名';
    $user = 'ユーザー名';
    $pass = 'パスワード';
        $pdo = new PDO
        ($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //テーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS tbtest5"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "passward char(32),"
        . "date TEXT"
        .");";
        $stmt = $pdo->query($sql);
        
        $date=date ( "Y年m月d日 H時i分s秒" );
        
        
        //編集フォームから送信があった場合
        if (!empty($_POST["edit_num"])){
            //編集番号と入力パスワードを変数に代入
            $edit_num=$_POST["edit_num"];
            $edit_pass=$_POST["edit_pass"];
            
            //テーブルの内容を配列に読み込む
            $sql = 'SELECT * FROM tbtest5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            
            //テーブルの各行に対して以下の処理を行う
            foreach ($results as $row){
                //idが編集番号と等しく，かつパスワードが一致する場合
                if ($row['id']==$edit_num && $row['passward']==$edit_pass){
                    $edit_name=$row['name'];
                    $edit_comment=$row['comment'];
                }
                //idが編集番号と等しいが，パスワードが一致しない場合
                if ($row['id']==$edit_num && $row['passward']!=$edit_pass){
                    echo "パスワードが違います<br>";
                }
            }
        }
        
        //投稿フォームから送信があったとき
        if(!empty($_POST["name"]) && !empty($_POST["comment"])){
            
            //パスワードの記入漏れがない場合
            if (!empty($_POST["post_pass"])){
                
                $name = $_POST["name"];
                $comment = $_POST["comment"]; 
                $post_pass=$_POST["post_pass"];
                
                //編集タグがある場合
                if (!empty($_POST["edit_tag"])){
                    //編集タグの番号とidが一致する行を，新しい投稿内容に書き換える
                    $edit_tag=$_POST["edit_tag"];
                    $id = $edit_tag; 
                    $sql = 'UPDATE tbtest5 SET name=:name,comment=:comment,passward=:passward,date=:date WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':passward',$post_pass, PDO::PARAM_STR);
                    $stmt->bindParam(':date',$date, PDO::PARAM_STR);          
                    $stmt->execute();
                }
                
                //編集タグがない場合
                else {
                    //テーブルに投稿内容を追記する
                    $sql = $pdo -> prepare("INSERT INTO tbtest5 (name, comment, passward, date) 
                    VALUES (:name, :comment, :passward, :date)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':passward', $post_pass, PDO::PARAM_STR);
                    $sql -> bindParam(':date',$date, PDO::PARAM_STR);
                    $sql -> execute();
                }
            }
            //パスワードの記入漏れがある場合
            else {
                echo "パスワードを設定してください<br>";
            }
        }
        
        //削除フォームから送信があった場合
        if (!empty($_POST["delete"])){
            $del=$_POST["delete"];
            $del_pass=$_POST["del_pass"];
            
            //テーブルの内容を配列に読み込む
            $sql = 'SELECT * FROM tbtest5';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //テーブルの各行に対して以下の処理を行う
            foreach ($results as $row){
                //idが削除番号と一致し，かつパスワードが一致した場合
                if ($row["id"]==$del && $row["passward"]==$del_pass){
                    $id = $del;
                    $sql = 'delete from tbtest5 where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }
                //idが削除番号と一致し，かつパスワードが一致しなかった場合
                if ($row["id"]==$del && $row["passward"]!=$del_pass){
                    echo "パスワードが違います<br>";
                }
            }
        }
    
    ?>

    <form action="" method="post">
        <input type="text" name="name" 
        value="<?php if (isset($edit_name)){echo $edit_name;} ?>" 
        placeholder="名前">
        
        <input type="text" name="comment" 
        value="<?php if (isset($edit_comment)){echo $edit_comment;} ?>"
        placeholder="コメント"
        size="50">
        
        <input type="password" name="post_pass" 
        value="<?php if (isset($edit_pass)){echo $edit_pass;} ?>"
        placeholder="パスワード">
        
        <input type="hidden" name="edit_tag" 
        value="<?php if (isset($edit_num)){echo $edit_num;} ?>"
        >
        
        <input type="submit" name="submit">
    </form>
    
    <form action="" method="post">    
        <input type="number" name="delete" placeholder="削除対象番号">
        <input type="text" name="del_pass" placeholder="パスワード">
        <input type="submit" name="submit" value="削除">
    </form>
    
    <form action="" method="post">
        <input type="number" name="edit_num" placeholder="編集対象番号">
        <input type="text" name="edit_pass" placeholder="パスワード">
        <input type="submit" name="submit" value="編集">
    </form>
    
    <br><br>
    
    <?php
    
        //テーブルの内容を配列に読み込む
        $sql = 'SELECT * FROM tbtest5';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        //パスワード以外の内容をブラウザに表示する
        foreach ($results as $row){
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['date'].'<br>';
            echo "<hr>";
        }
    
    ?>
    
</body>
</html>