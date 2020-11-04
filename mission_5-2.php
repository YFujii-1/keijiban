<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-2</title>
</head>
<body>
    <?php
    //変数代入
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $hd = $_POST["hd"];
    $Npw = $_POST["Npw"]; //入力用のパスワード
    $number = $_POST["number"];
    $Spw = $_POST["Spw"]; //送信されたパスワード
    $Hnumber = $_POST["Hnumber"];
    
    //データベース接続
    $dsn = 'mysql:dbname=tb2****6db;host=localhost';
	$user = 'tb-*****6';
	$password = 'm*******Ku';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
    //編集前の名前、コメントの取得（はじまり）
    $sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	//パスワードを取得
	foreach ($results as $row){
	    if($row['id'] == $number || $row['id'] == $Hnumber){
	        $pw1 = $row['password'];  //一致するか判断するためのパスワード
	        $name1 = $row['name'];        //編集のフォームに表示する用も同時に取得
	        $comment1 = $row['comment'];
	        $hdnum1 = $row['id'];
	    }
	}
    
    //編集番号とパスワードが一致するか
    if(!empty($Hnumber) && !empty($Spw)){
        if($pw1 == $Spw){
            $Hname = $name1;
            $Hcomment = $comment1;
            $hdnum = $hdnum1;
        }else{
            $miss = "パスワードが違います。";
        }
    }
    //おわり
    ?>
    
    	<form action="" method="post">
        <input type="text" name="name"  value="<?php echo $Hname; ?>" placeholder="名前">
        <br>
        <input type="text" name="comment"  value="<?php echo $Hcomment; ?>" placeholder="コメント">
        <br>
        <input type="text" name="Npw"  placeholder="パスワード">
        <br>
        <input type="hidden" name="hd"  value="<?php echo $hdnum; ?>">
        <input type="submit" value="送信">
        <br><br>     
    </form>
    <form action="" method="post">
        <input type="number" name="number" placeholder="削除番号">
        <br>
        <input type="text" name="Spw"  placeholder="パスワード">
        <br>
        <input type="submit" value="削除" >
        <br><br>
    </form>
    <form action="" method="post">
        <input type="number" name="Hnumber"  placeholder="編集番号">
        <br>
        <input type="text" name="Spw"  placeholder="パスワード">
        <br>
        <input type="submit" value="編集" >
    </form>
    
    <?php
	//テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment text,"
	. "password text" 
	.");";
	$stmt = $pdo->query($sql);
	
	//テーブルに名前、コメント、パスワードを入力
	if(!empty($name) && !empty($comment) && !empty($Npw) && empty($hd)){
	    $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, password) 
	    VALUES (:name, :comment, :password)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
 	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	    $sql -> bindParam(':password', $Npw, PDO::PARAM_STR);
	    $sql -> execute();
	}
	
	
	//削除
	if(!empty($number) && !empty($Spw)){
	    if($Spw == $pw1){
	        $id = $number;
	        $sql = 'delete from tbtest where id=:id';
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	        $stmt->execute();
	    }else{
	        $miss = "パスワードが違います。";
	    }
	}
	
	//編集入力
	if(!empty($name) && !empty($comment) && !empty($Npw) && !empty($hd)){
	    $id = $hd; //変更する投稿番号
	    $sql = 'UPDATE tbtest SET name=:name,comment=:comment,password=:password WHERE id=:id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	    $stmt->bindParam(':password', $Npw, PDO::PARAM_STR);
	    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	    $stmt->execute();
	}
	
	//表示
	$sql = 'SELECT * FROM tbtest';
	$stmt1 = $pdo->query($sql);
	$results1 = $stmt1->fetchAll();
	foreach ($results1 as $row1){
		//$rowの中にはテーブルのカラム名が入る
		echo $row1['id'].',';
		echo $row1['name'].',';
		echo $row1['comment'].'<br>';
	    echo "<hr>";
	}
	
	//「パスワードが違います」表示
	if(!empty($miss)){
	    echo $miss;
	}

	?>
    
</body>
