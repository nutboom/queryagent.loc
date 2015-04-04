<?php
header('Content-Type: text/html; charset=utf-8');

$config		=	require(dirname(__FILE__).'/protected/config/local/main.php');
$pdo		=	new PDO($config['db']['connectionString'], $config['db']['username'], $config['db']['password']);
$pdo->exec("set names utf8");

$delete = array();
$sql = $pdo->query("SELECT * FROM `tbl_mail_cron` ORDER BY `id` ASC LIMIT 0, 100");
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
	mail($row['mail'], $row['subject'], $row['content'], $row['headers']);
	array_push($delete, $row['id']);
}

$sql = $pdo->prepare("
	DELETE FROM `tbl_mail_cron`
	WHERE `id` IN (".implode(",", $delete).")
");
$sql->execute();
?>