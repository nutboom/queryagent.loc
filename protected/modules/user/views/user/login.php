<?php
Yii::app()->clientScript->registerCssFile("/css/auth-style.css");
Yii::app()->clientScript->registerScriptFile("/js/auth-script.js");

$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Login");
$this->breadcrumbs=array(
	UserModule::t("Login"),
);
?>

<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'verticalForm',
	//'htmlOptions'=>array('class'=>'well'),
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
       <div class="cl-mcont">
            <div class="col-sm-12">
                <form class="register-form">
                    <div class="login-form" id="login-form">
                        <div class="form-group">
                            <div class="col-sm-12 cetnerText">
                                <h2><?php echo UserModule::t("Authorization"); ?></h2>
                            </div>
                        </div>

                <?php if(Yii::app()->user->hasFlash('loginMessage')): ?>
                    <div class="success">
                        <?php echo Yii::app()->user->getFlash('loginMessage'); ?>
                    </div>
                <?php endif; ?>

                <?php echo $form->errorSummary($model); ?>



                        <div class="form-group">
                            <div class="col-sm-12"><?php echo $form->textField($model, 'username', array('class'=>'form-control','placeholder'=>UserModule::t("username"))); ?></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12"><?php echo $form->passwordField($model, 'password', array('class'=>'form-control','placeholder'=>UserModule::t("password"))); ?></div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="form-group">
                        	<div class="col-sm-12">
                        		<a href="<?php echo $this->createUrl("/registration"); ?>"><?php echo UserModule::t("Registration"); ?></a>
                        		&nbsp;&nbsp;
                        		<a href="<?php echo $this->createUrl("/recovery"); ?>"><?php echo UserModule::t("Lost Password?"); ?></a>
                        	</div>
                        	<div class="clearfix"></div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <div class="col-sm-8">
                                    <?php echo $form->checkbox($model, 'rememberMe', array('class'=>'repCheckbox')); ?>
                                    <div class="checkbox-layer"></div>
                                    <label for="remember">Запомнить меня</label>
                                </div>
                                <div class="col-sm-4">
                                    <button class="btn btn-danger">Войти</button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </form>
            </div>
        </div>
<?php $this->endWidget(); ?>

<?php
$form = new CForm(array(
    'elements'=>array(
        'username'=>array(
            'type'=>'text',
            'maxlength'=>32,
        ),
        'password'=>array(
            'type'=>'password',
            'maxlength'=>32,
        ),
        'rememberMe'=>array(
            'type'=>'checkbox',
        )
    ),

    'buttons'=>array(
        'login'=>array(
            'type'=>'submit',
            'label'=>'Login',
        ),
    ),
), $model);
?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48595160-1', 'queryagent.ru');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter23332327 = new Ya.Metrika({id:23332327,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<!-- /Yandex.Metrika counter -->