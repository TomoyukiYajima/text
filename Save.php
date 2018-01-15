<?php
// SQL
//-------------------------------------------------
//準備
//-------------------------------------------------
$dsn  = 'mysql:dbname=noveldb;host=127.0.0.1';   //接続先
$user = 'root';         //MySQLのユーザーID
$pw   = 'H@chiouji1';   //MySQLのパスワード

// データをmysqlに一時保存する
$sql = 'update save set dataid=:ID,playername=:NAME,text=:TEXT,char1=:CHAR1,char2=:CHAR2,bg=:BGSRC,bgm=:BGMSRC';
	
$dbh = new PDO($dsn, $user, $pw);   //接続
$sth = $dbh->prepare($sql);         //SQL準備
$sth->bindValue(':ID', $_POST['id'], PDO::PARAM_INT);
$sth->bindValue(':NAME', $_POST['name'], PDO::PARAM_STR);
$sth->bindValue(':TEXT', $_POST['text'], PDO::PARAM_STR);
$sth->bindValue(':CHAR1', $_POST['ch1'], PDO::PARAM_STR);
$sth->bindValue(':CHAR2', $_POST['ch2'], PDO::PARAM_STR);
$sth->bindValue(':BGSRC', $_POST['bgsrc'], PDO::PARAM_STR);
$sth->bindValue(':BGMSRC', $_POST['bgmsrc'], PDO::PARAM_STR);
$sth->execute();                    //実行

?>

<!DOCTYPE html>

<html>

<head> 
	<meta charset="utf8">
	<title>ノベルゲーム</title>	
	<link rel="stylesheet" href="style.css">
	<style>
		#novelwindow > form > button{
			position: absolute;
			top: 550px;
			left: 600px;
			
			width: 200px;
			height: 50px;
		}
	</style>
</head>

<body>
	<section id="novelwindow">
		<section id="subtextwindow">
			<h1>データのセーブ</h1>	
		</section>
		
		<section id="datawindow">
			<form action="SaveOK.php" method="POST">
				<button name="dataID" value="1"><img id="sceneimage" src="image/bg/seikimathu.png">データ1</button>
			</form>
			<form action="SaveOK.php" method="POST">
				<button name="dataID" value="2"><img id="sceneimage" src="image/bg/seikimathu.png">データ2</button>
			</form>
			<form action="SaveOK.php" method="POST">
				<button name="dataID" value="3"><img id="sceneimage" src="image/bg/seikimathu.png">データ3</button>
			</form>
			<form action="SaveOK.php" method="POST">
				<button name="dataID" value="4"><img id="sceneimage" src="image/bg/seikimathu.png">データ4</button>
			</form>
		</section>
		
		<section id="bottomwindow">
			<form action="title.html" method="GET">
				<button id="titleButton">タイトルに戻る</button>
			</form>
			<form action="Novel.php" method="GET">
				<button id="backButton">戻る</button>
			</form>
		</section>
	</section>
</body>

</html>
