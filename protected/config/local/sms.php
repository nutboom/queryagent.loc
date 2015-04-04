<?php
return Array(
	"sms.ru" => Array(
		"url"			=> "http://sms.ru/sms/send",
		"from"			=> "QueryAgent",
		"api_id"		=> "6b0bd472-2bbc-2144-69e4-00ca1bdfb482",
		"prepare"		=> "_sms_prepare_SMSRU",
		"check"			=> "_sms_check_SMSRU",
	),

	"sms24x7.ru" => Array(
		"url"			=> "http://api.sms24x7.ru/",
		"login"			=> "shavlukevich@gmail.com",
		"password"		=> "Fa1SNNc",
		"from"			=> "QueryAgent",
		"prepare"		=> "_sms_prepare_SMS24x7RU",
		"check"			=> "_sms_check_SMS24x7RU",
	),
);