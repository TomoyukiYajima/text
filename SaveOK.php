<?php
// SQL
//-------------------------------------------------
//準備
//-------------------------------------------------
$dsn  = 'mysql:dbname=noveldb;host=127.0.0.1';   //接続先
$user = 'root';         //MySQLのユーザーID
$pw   = 'H@chiouji1';   //MySQLのパスワード

// データ
$datas = [];
// 保存したID
$saveID = $_POST['dataID'];

GetData($dsn, $user, $pw);

Save($dsn, $user, $pw);

// データの取得
function GetData($dsn, $user, $pw){
	// データをmysqlに一時保存する
	$sql = 'select * from save';
		
	$dbh = new PDO($dsn, $user, $pw);   //接続
	$sth = $dbh->prepare($sql);         //SQL準備
	$sth->execute();                    //実行
	
	// SQLのデータを配列に入れる
	global $datas;
	$buff = $sth->fetch(PDO::FETCH_ASSOC);
	// データを入れる
	array_push($datas, $buff);
}

// データをSQLに保存
function Save($dsn, $user, $pw){
	// $sql = 'update saveData set playerName=:NAME,sceneID=:SCID,char1:=CHAR1,char2=:CHAR2,bg=:BG,bgm=:BGM where dataID=:ID';
	//$sql = 'select * from saveData where dataID=:ID';
	$sql = 'update saveData set playerName=:NAME,sceneID=:SCID,text=:TEXT,char1=:CHAR1,char2=:CHAR2,bg=:BG,bgm=:BGM where dataID=:ID';
	
	// SQLにデータを入れる
	global $datas;		
	$dbh = new PDO($dsn, $user, $pw);   //接続
	$sth = $dbh->prepare($sql);         //SQL準備
	$sth->bindValue(':NAME', $datas[0]['playername'], PDO::PARAM_STR);
	$sth->bindValue(':SCID', $datas[0]['dataid'], PDO::PARAM_INT);
	$sth->bindValue(':TEXT', $datas[0]['text'], PDO::PARAM_STR);
	$sth->bindValue(':CHAR1', $datas[0]['char1'], PDO::PARAM_STR);
	$sth->bindValue(':CHAR2', $datas[0]['char2'], PDO::PARAM_STR);
	$sth->bindValue(':BG', $datas[0]['bg'], PDO::PARAM_STR);
	$sth->bindValue(':BGM', $datas[0]['bgm'], PDO::PARAM_STR);
	$sth->bindValue(':ID', $_POST['dataID'], PDO::PARAM_INT);
	$sth->execute();                    //実行
}

// 保存したIDの取得
function getSaveID(){
	global $saveID;
	return $saveID;
}
?>

<!DOCTYPE html>

<html>

<head> 
	<meta charset="utf8">
	<title>ノベルゲーム</title>
	<link rel="stylesheet" href="style.css">
	<style>
		#novelwindow > h1{
			margin-top: 200px;
		}
		
		#novelwindow > form > button{
			width: 300px;
			height: 100px;
			margin-bottom: 30px;
		}
	</style>
</head>

<body>
	<section id="novelwindow">
		<section id="textwindow">
			<h1>セーブ完了！</h1>
		</section>
		
		<form action="Novel.php" method="POST">
			<button id="button" name="dataID" value="1">ゲームに戻る</button>
		</form>
		<form action="title.html" method="GET">
			<button>タイトルに戻る</button>
		</form>
		<audio id="se" src="sound/se/punch.ogg" autoplay></audio>
	</section>
	
	<script>
		var saveID = <?= json_encode(getSaveID()) ?>;
		// ボタンのvalueの変更
		var btn = document.querySelector("#button");
		btn.setAttribute('value', saveID);
	</script>
	
</body>

</html>
