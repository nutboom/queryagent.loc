<?php
$config		=	require(dirname(__FILE__).'/protected/config/local/main.php');
$pdo		=	new PDO($config['db']['connectionString'], $config['db']['username'], $config['db']['password']);

$sql = $pdo->prepare("SELECT * FROM `tbl_transactions` WHERE `id` = :id");
$sql->bindValue(":id", $_POST['InvId'], PDO::PARAM_INT);
$sql->execute();
$data = $sql->fetch(PDO::FETCH_ASSOC);

$summ1	=	$data['summ']*1;
$summ2	=	$_POST['OutSum']*1;
$res	=	$summ1 != $summ2;

// корректность суммы
if ($res) {
	echo "bad summ\n";
	exit();
}

// генерируем нашу подпись
$rbxPassword=	"9KmqOpVmk1";
#$crc		=	strtoupper(md5($_POST["OutSum"].":".$_POST["InvId"].":".$rbxPassword.":Shp_item=".$_POST["Shp_item"]));
if(isset($_POST["Shp_1"]))
	$crc		=	strtoupper(md5($_POST["OutSum"].":".$_POST["InvId"].":".$rbxPassword.":Shp_1=".$_POST["Shp_1"]));
else
	$crc		=	strtoupper(md5($_POST["OutSum"].":".$_POST["InvId"].":".$rbxPassword));

// success
echo "OK{$_POST['InvId']}\n";

$query = $pdo->prepare("
	UPDATE `tbl_transactions` SET
		`status`	=	'payed'
	WHERE
		`id` = :id
");
$query->bindValue(":id", $_POST['InvId'], PDO::PARAM_INT);
$query->execute();

$query = $pdo->prepare("
	UPDATE `tbl_users` SET
		`balance`	=	`balance` + :summ
	WHERE
		`id` = :user
");
$query->bindValue(":user", $data['user'], PDO::PARAM_INT);
$query->bindValue(":summ", $data['summ']);
$query->execute();
?>


