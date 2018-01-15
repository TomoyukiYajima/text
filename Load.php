<?php
// SQL
//-------------------------------------------------
//準備
//-------------------------------------------------
$dsn  = 'mysql:dbname=noveldb;host=127.0.0.1';   //接続先
$user = 'root';         //MySQLのユーザーID
$pw   = 'H@chiouji1';   //MySQLのパスワード

// 戻るシーン
$sceneName = $_POST['sceneName'];
$saveDatas = [];

if($sceneName === 'Novel.php'){
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
}

// セーブデータの読み込み
$sql = 'select * from saveData';
		
$dbh = new PDO($dsn, $user, $pw);   //接続
$sth = $dbh->prepare($sql);         //SQL準備
$sth->execute();                    //実行

while(true){
	$buff = $sth->fetch(PDO::FETCH_ASSOC);
	if ($buff === false) break;
	// データを入れる
	array_push($saveDatas, $buff);
}

// セーブデータの取得
function getSaveData(){
	global $saveDatas;
	return $saveDatas;
}

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
			<h1>データのロード</h1>	
		</section>
		
		<section id="datawindow">
			<form action="Novel.php" method="POST">
				<button id="data1" name="dataID" value="1"><img id="sceneimage" src="image/bg/seikimathu.png">データ1</button>
			</form>
			<form action="Novel.php" method="POST">
				<button id="data2" name="dataID" value="2"><img id="sceneimage" src="image/bg/seikimathu.png">データ2</button>
			</form>
			<form action="Novel.php" method="POST">
				<button id="data3" name="dataID" value="3"><img id="sceneimage" src="image/bg/seikimathu.png">データ3</button>
			</form>
			<form action="Novel.php" method="POST">
				<button id="data4" name="dataID" value="4"><img id="sceneimage" src="image/bg/seikimathu.png">データ4</button>
			</form>
		</section>
		
		<section id="bottomwindow">
			<form action="title.html" method="GET">
				<button id="titleButton">タイトルに戻る</button>
			</form>
			
			<form id="back" action="title.html" method="GET">
				<button id="backButton" name="backScene" value="load">戻る</button>
			</form>
		</section>
	</section>
	
	<audio id="se" src="sound/se/datadecision.ogg" preload></audio>
	
	<script>
		// 遷移するシーンの変更
		var backform = document.querySelector("#back");
		backform.setAttribute('action', '<?= $sceneName ?>');
		
		// データボタンの設定
		var datas = <?= json_encode(getSaveData()) ?>;
		var data1btn = document.querySelector("#data1");
		if(datas[0]['playerName'] == '') data1btn.setAttribute("disabled", "disabled");
		else data1btn.innerHTML = datas[0]['playerName'];
		var data2btn = document.querySelector("#data2");
		if(datas[1]['playerName'] == '') data2btn.setAttribute("disabled", "disabled");
		else data1btn.innerHTML = datas[1]['playerName'];
		var data3btn = document.querySelector("#data3");
		if(datas[2]['playerName'] == '') data3btn.setAttribute("disabled", "disabled");
		else data1btn.innerHTML = datas[2]['playerName'];
		var data4btn = document.querySelector("#data4");
		if(datas[3]['playerName'] == '') data4btn.setAttribute("disabled", "disabled");
		else data1btn.innerHTML = datas[3]['playerName'];
		
		// ボタンが押された時の処理
		var btn = document.querySelector("#backButton");
		btn.addEventListener("click", function() { playSE(); } );
		var titlebtn = document.querySelector("#titleButton");
		titlebtn.addEventListener("click", function() { playSE(); } );
		// SEの再生
		function playSE(){
			var se = document.querySelector("#se");
			se.play();
		}
	</script>
</body>

</html>
