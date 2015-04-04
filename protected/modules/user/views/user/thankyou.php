<?php
$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Registration");
$this->breadcrumbs=array(
	UserModule::t("Registration"),
);
?>

<style>
table, td, tr {
	border: none !important;
	background: none !important;
	font-size: 16px !important;
}
.form-text {
    font-size: 12px;
    background-color: #ffffff;
    border: 1px solid #b2d4dc;
    color: #555555;
    display: block;
    font-size: 14px;
    height: 34px;
    line-height: 1.42857;
    padding: 6px 12px;
    vertical-align: middle;
    width: 100%;
}

.form-text:focus {
	border: 1px solid #e36974;
}


.formerer {
    background: none repeat scroll 0 0 #fff;
    font-family: 'Roboto',sans-serif;
    min-width: 500px;
    padding: 15px 50px;
    font-size: 16px !important;
    font-color: #a7a7a7;
}

.formerer img {
    margin: 20px 0px;
}

h2 {
	border-bottom: 1px solid #4fc1e9;
	padding-bottom: 5px;
	margin-bottom: 20px;
	font-variant: small-caps;
	color: #5b7180;
}

.buttonsss {
	background: #e36974;
	border-radius: 5px;
	border: 1px #e36974 solid;
	font-variant: small-caps;
	color: #fff;
	padding: 10px 30px;
	font-size: 20px;
	text-decoration: none;
}

.buttonsss:hover {
	text-decoration: none;
}

.okkkeeyey {
	color: #34a3d3;
	text-align: center;
	font-size: 26px;
}
</style>


<div class="cl-mcont">
<div class="col-sm-2"></div>
	<div class="col-sm-8">
		<div class="formerer">
	        <h2><?php echo UserModule::t("Registration"); ?></h2>
	        <center>
	            <div class="okkkeeyey">Регистрация завершена</div>
	            <br /><br />

	            <img src="/images/sucreg.png">
	            <br /><br />

	            Пожалуйста проверьте свой электронный ящик или войдите в личный кабинет
	            <br /><br />

	            <a href="<?php echo $url; ?>" class="buttonsss">Выполнить вход</a>
            </center>
        </div>
	</div>
</div>