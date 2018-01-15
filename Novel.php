<?php
// SQL
//-------------------------------------------------
//準備
//-------------------------------------------------
$dsn  = 'mysql:dbname=noveldb;host=127.0.0.1';   //接続先
$user = 'root';         //MySQLのユーザーID
$pw   = 'H@chiouji1';   //MySQLのパスワード

$command = 'TXT';						// 実行するコマンド
$value = 'テキスト';					// 表示する値(テキスト)
$scenarioSth;
$datas = [];							// SQLのデータ
$loadDatas = [];						// ロードデータ
$replayDatas = [];						// リプレイデータ
//$sceneId = 0;						// シナリオID
//nextAction();

// データがなければロード
if( array_key_exists('playername', $_GET) ){
	// わざと全書き換えを行う
	// プレースフォルダにする
	$sql = 'update save set playername=:name';
	
	$dbh = new PDO($dsn, $user, $pw);   //接続
	$sth = $dbh->prepare($sql);         //SQL準備
	$sth->bindValue(':name', $_GET['playername'], PDO::PARAM_STR);
	$sth->execute();                    //実行
	// SQLのデータを保存
	setScinario($dsn, $user, $pw);
	
	$playername = $_GET['playername'];
	// 名前が空欄なら、規定の名前を入れる
	if ($playername === '') $playername = 'ななし';
}
else{
	if( array_key_exists('dataID', $_POST) ){
		// セーブデータから再読み込み
		// SQLからデータの取得
		$sql = 'select * from saveData where dataID=:ID';
		
		$dbh = new PDO($dsn, $user, $pw);   //接続
		$sth = $dbh->prepare($sql);         //SQL準備
		$sth->bindValue(':ID', $_POST['dataID'], PDO::PARAM_INT);
		$sth->execute();                    //実行
		
		global $loadDatas;
		// 1行のみを取得している
		$buff = $sth->fetch(PDO::FETCH_ASSOC);
		// データを入れる
		array_push($loadDatas, $buff);
		$playername = $buff['playerName'];
		
		// SQLのデータを保存
		setScinario($dsn, $user, $pw);
	}
	else{
		// データの再読み込み
		// データの取得
		$sql = 'select * from save';
		
		$dbh = new PDO($dsn, $user, $pw);   //接続
		$sth = $dbh->prepare($sql);         //SQL準備
		$sth->execute();                    //実行
		// 1行のみを取得している
		$buff = $sth->fetch(PDO::FETCH_ASSOC);
		global $replayDatas;
		// データを入れる
		array_push($replayDatas, $buff);
		$playername = $buff['playername'];
		// SQLのデータを保存
		setScinario($dsn, $user, $pw);
	}
}

// シナリオSQLの設定
function setScinario($dsn, $user, $pw){
	// シナリオの取得
	$sql = 'select * from scenario';

	$dbh = new PDO($dsn, $user, $pw);   //接続
	$sth = $dbh->prepare($sql);         //SQL準備
	$sth->execute();                    //実行
	
	// SQLのデータを配列に入れる
	global $datas;
	while(true){
		$buff = $sth->fetch(PDO::FETCH_ASSOC);
		if ($buff === false) break;
		// データを入れる
		//array_push($datas, $buff['value']);
		array_push($datas, $buff);
	}
	
	//print_r($datas);
}

// テキストボックスの値を設定します
function nextAction(){
	//global $sceneId, $datas;
	//$prevId = $sceneId;
	//$sceneId = $datas[$prevId]['nextid'];
}

function getData($value){
	global $datas, $sceneId;
	return $datas[$sceneId][$value];
}

function getDatas(){
	global $datas;
	return $datas;
}

function getID(){
	global $sceneId;
	return $sceneId;
}

function getLoadData(){
	global $loadDatas;
	return $loadDatas;
}

function getLoadDataSize(){
	global $loadDatas;
	return count($loadDatas);
}

function getReplayData(){
	global $replayDatas;
	return $replayDatas;
}

function getReplayDataSize(){
	global $replayDatas;
	return count($replayDatas);
}

// データをmysqlに一時保存する
function dataSave(){
	$sql = 'update save set dataid=:id,char1=:ch1,char2:ch2,bg=:bgsrc,bgm=:bgmsrc';
	
	$dbh = new PDO($dsn, $user, $pw);   //接続
	$sth = $dbh->prepare($sql);         //SQL準備
	$sth->bindValue(':id', 0, PDO::PARAM_STR);
	$sth->bindValue(':ch1', '', PDO::PARAM_STR);
	$sth->bindValue(':ch2', '', PDO::PARAM_STR);
	$sth->bindValue(':bgsrc', '', PDO::PARAM_STR);
	$sth->bindValue(':bgmsrc', '', PDO::PARAM_STR);
	$sth->execute();                    //実行
}
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf8">
	<title>ノベルゲーム</title>
	<link rel="stylesheet" href="style.css">
	<style>
		#novelwindow{
			<!-- コメントアウト -->
			background-color: #114514;
		}
		
		#messagewindow{
			position: absolute;
			z-index: 10;
			top: 350px;
			left: 75px;
			
			border: 1px solid blue;
			width: 650px;
			height: 200px;
			
			font-size: 22pt;
			padding: 5px;
			text-align: left;
			
			background-color: rgba(230, 230, 230, 0.7);
		}
		
		#char1{
			height: 500px;	
			<!-- 今自分の位置がどうなっているか -->
			<!--position: relative;-->
			<!-- 深さの指定 -->
			<!--z-index: 100;-->
		}
		#char2{
			height: 500px;	
		}
		
		table.timage{
			border-spacing: 13px 20px;
		}
	</style>
	
	
</head>

<body>
	<div id="novelwindow">
		<!--キャラクター画像-->
		<table class="timage">
			<tr>
				<td><img id="char1" src="image/char/man.png" style="visibility:visible"></td>
				<td><img id="char2" src="" style="visibility:hidden"></td>
			</tr>
		</table>
		
		<div id="buttonwindow">
			<!--ボタン-->
			<form action="Load.php" method="POST">
				<input id="sceneIDLoad" type='hidden' name='id' value=0>
				<input id="playerNameLoad" type='hidden' name='name' value=''>
				<input id="sceneTextLoad" type='hidden' name='text' value=''>
				<input id="charimg1Load" type='hidden' name='ch1' value=''>
				<input id="charimg2Load" type='hidden' name='ch2' value=''>
				<input id="bgsrcimgLoad" type='hidden' name='bgsrc' value=''>
				<input id="bgmsrcimgLoad" type='hidden' name='bgmsrc' value=''>
				<button id="load" name="sceneName" value="Novel.php">ロード</button>
			</form>
			<form action="Save.php" method="POST">
				<input id="sceneID" type='hidden' name='id' value=0>
				<input id="playerName" type='hidden' name='name' value=''>
				<input id="sceneText" type='hidden' name='text' value=''>
				<input id="charimg1" type='hidden' name='ch1' value=''>
				<input id="charimg2" type='hidden' name='ch2' value=''>
				<input id="bgsrcimg" type='hidden' name='bgsrc' value=''>
				<input id="bgmsrcimg" type='hidden' name='bgmsrc' value=''>
				<button id="save">セーブ</button>
			</form>
		</div>
		
		<div id="messagewindow">
			<div id="message">
				
			</div>
		</div>
	</div>
	
	<!--SEの再生-->
	<audio id="se" src="sound/se/punch.ogg" preload></audio>
	<audio id="bgm" src="sound/bgm/seikimathu.ogg" autoplay></audio>
	
	<script>
		// プレイヤー名の取得
		var playername = "<?= $playername ?>";
		// シナリオ定義
		// 配列を持って来る際に、json_encode()が必須になる
		var scenario = <?= json_encode(getDatas()) ?>;
		var sceneID = 0;
		var dataSize = "<?= getLoadDataSize() ?>"
		
		var box = document.querySelector("#messagewindow");
		var char1 = document.querySelector("#char1");
		var char2 = document.querySelector("#char2");
		var se = document.querySelector("#se");
		var bgm = document.querySelector("#bgm");
		var bgImg = 'image/bg/seikimathu.png';
		var bgmLink = 'sound/bgm/seikimathu.ogg';
		
		// ロード時の場合
		if(dataSize > 0){
			// シナリオIDなどを更新
			sceneID = "<?= getLoadData()[0][sceneID] ?>";
			// シーンの画像などを変更
			changeWindow();
		}
		else{
			dataSize = "<?= getReplayDataSize() ?>"
			if(dataSize > 0){
				// リプレイの場合
				// シナリオIDなどを更新
				sceneID = "<?= getReplayData()[0][dataid] ?>";
				// シーンの画像などを変更
				prevWindow();
			}
		}
		// シーンアクションの実行
		sceneAction();
		
		// メッセージボックスがクリックされた時
		box.addEventListener("click", function(){
				// 次のシーンIDに変更
				sceneID = scenario[sceneID]['nextid'];
				// シーンアクションの実行
				sceneAction();
			}
		);
		
		// ロードボタンが押された時の処理
		var btnLoad = document.querySelector("#load");
		btnLoad.addEventListener("click", function(){
				pauseDataLoad();
			}
		);
		// セーブボタンが押された時の処理
		var btnSave = document.querySelector("#save");
		btnSave.addEventListener("click", function(){
				pauseData();
			}
		);
		
		// シーンアクション
		// window.location.href = 'title.html';
		function sceneAction(){
			var command = scenario[sceneID]['command'];
			var value = scenario[sceneID]['value'];
			switch(command){
				case "CHAR1": setImage(char1, value); break;
				case "CHAR2": setImage(char2, value); break;
				case "TXT":
					value = value.replace(
						/##NAME##/g, 
						"<span style='color:red'>" + playername + "</span>"
						);
					box.innerHTML = value; break;
				case "SE": setSound(se, value); break;
				case "BGM": setSound(bgm, value); bgmLink = value; break;
				case "BG": changeBG(value); bgImg = value; break;
				case "END": window.location.href = 'Ending1.html'; break;
			}
		}
		
		// 画像の設定
		function setImage(charactar, value){
			// hidden
			if(value == '') {
				charactar.setAttribute("style", "visibility:hidden");
				return;
			}
			charactar.setAttribute("src", value);
			// 画像のリンクがあるかで表示の切り替えをする
			charactar.setAttribute("style", "visibility:visible");
		}
		
		// サウンドの設定
		function setSound(sound, value){
			if(value == '') {
				if(sound.getAttribute("src") != '') sound.pause();
			}
			sound.setAttribute("src", value);
			if(value != '') sound.play();
		}
		
		// 背景画像の変更
		function changeBG(value){
			var novelWindow = document.querySelector("#novelwindow");
			var str = 'url(' + value +')';
			novelWindow.style.backgroundImage = str;
		}
		
		// データの一時保存
		function pauseDataLoad(){
			// データを一時保存する
			// さすがに効率が悪すぎるので、もっと良い方法があるはず
			var ele1 = document.querySelector("#sceneIDLoad");
			ele1.setAttribute('value', sceneID);
			var ele2 = document.querySelector("#playerNameLoad");
			ele2.setAttribute('value', playername);
			var ele3 = document.querySelector("#sceneTextLoad");
			ele3.setAttribute('value', box.innerHTML);
			var ele4 = document.querySelector("#charimg1Load");
			ele4.setAttribute('value', char1.getAttribute("src"));
			var ele5 = document.querySelector("#charimg2Load");
			ele5.setAttribute('value', char2.getAttribute("src"));
			var ele6 = document.querySelector("#bgsrcimgLoad");
			ele6.setAttribute('value', bgImg);
			var ele7 = document.querySelector("#bgmsrcimgLoad");
			ele7.setAttribute('value', bgmLink);
		}
		// データの一時保存
		function pauseData(){
			// データを一時保存する
			// さすがに効率が悪すぎるので、もっと良い方法があるはず
			var ele1 = document.querySelector("#sceneID");
			ele1.setAttribute('value', sceneID);
			var ele2 = document.querySelector("#playerName");
			ele2.setAttribute('value', playername);
			var ele3 = document.querySelector("#sceneText");
			ele3.setAttribute('value', box.innerHTML);
			var ele4 = document.querySelector("#charimg1");
			ele4.setAttribute('value', char1.getAttribute("src"));
			var ele5 = document.querySelector("#charimg2");
			ele5.setAttribute('value', char2.getAttribute("src"));
			var ele6 = document.querySelector("#bgsrcimg");
			ele6.setAttribute('value', bgImg);
			var ele7 = document.querySelector("#bgmsrcimg");
			ele7.setAttribute('value', bgmLink);
		}
		
		// シーンの変更
		function changeWindow(){
			var datas = <?= json_encode(getLoadData()[0]) ?>;
			// テキストの変更
			box.innerHTML = datas['text'];
			// 画像の設定
			setImage(char1, datas['char1']);
			setImage(char2, datas['char2']);
			// 背景画像の変更
			var novelWindow = document.querySelector("#novelwindow");
			novelWindow.style.backgroundImage = 'url(<?= getLoadData()[0][bg] ?>)';
			// BGMの変更
			setSound(bgm, datas['bgm']);
		}
		
		// 前の状態に戻す
		function prevWindow(){
			var datas = <?= json_encode(getReplayData()[0]) ?>;
			// テキストの変更
			box.innerHTML = datas['text'];
			// 画像の設定
			setImage(char1, datas['char1']);
			setImage(char2, datas['char2']);
			// 背景画像の変更
			var novelWindow = document.querySelector("#novelwindow");
			novelWindow.style.backgroundImage = 'url(<?= getReplayData()[0][bg] ?>)';
			// BGMの変更
			setSound(bgm, datas['bgm']);
		}
	</script>
</body>

</html>
