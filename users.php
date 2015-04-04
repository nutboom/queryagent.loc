<?php
mb_http_input("UTF-8");
mb_http_output("UTF-8");
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

Header("Content-type: text/csv");
Header("Content-Disposition: attachment; filename = user.csv");

require(dirname(__FILE__).'/mailer.php');
$mailer_form = "admin@queryagent.ru";

$config		=	require(dirname(__FILE__).'/protected/config/local/main.php');
$pdo		=	new PDO($config['db']['connectionString'], $config['db']['username'], $config['db']['password']);
$pdo->exec("set names utf8");

# за неделю
$sql = $pdo->query("SELECT `tbl_profiles`.`last_name` as `last_name`, `tbl_profiles`.`first_name` as `first_name`, `tbl_users`.`email` as `email` FROM `tbl_profiles`, `tbl_users` WHERE `tbl_profiles`.`user_id` = `tbl_users`.`id` ORDER BY `tbl_users`.`id` ASC");
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    echo '"'.$row['last_name'] . '";"' . $row['first_name'] . '";"' . $row['email'] . '"' . "\n";
}


?>


