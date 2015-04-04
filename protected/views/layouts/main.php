<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="/favicon.ico">

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<!-- Bootstrap core CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap/dist/css/bootstrap.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.gritter/css/jquery.gritter.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/Fonts/font-awesome-4/css/font-awesome.min.css">

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller/nanoscroller.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.easypiechart/jquery.easy-pie-chart.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.switch/bootstrap-switch.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.datetimepicker/css/bootstrap-datetimepicker.min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.select2/select2.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.slider/css/slider.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/js/fuelux/css/fuelux.css">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/bootstro.css" />

    <link href='//fonts.googleapis.com/css?family=Roboto:400,100,300,500,700&subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>


	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.flot/jquery.flot.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.flot/jquery.flot.pie.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.flot/jquery.flot.resize.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.flot/jquery.flot.labels.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.flot/jquery.flot.time.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.icheck/icheck.min.js"></script>

	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.cookie.js"></script>

	<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstro.js"></script>


	<style>
		#head-nav {
			background: <?php echo Branding::getTopColor(); ?>;
		}

		.navbar {
			background-color: <?php echo Branding::getTopColor(); ?>;
		}

		.cl-navblock {
			background: <?php echo Branding::getLeftColor(); ?>;
		}

		.fuelux .wizard ul li.active, .nCForm-group, #searchresults li:hover {
			background: <?php echo Branding::getLeftColor(); ?>;
		}

		.fuelux .wizard ul li.active .chevron:before {
			border-left: 14px solid <?php echo Branding::getLeftColor(); ?>;
		}

		.cl-mcont > .span-20 li a{
		    background: <?php echo Branding::getLeftColor(); ?>;
		}

		.fuelux .wizard ul li.active .chevron:before {
        	border-left: 14px solid <?php echo Branding::getLeftColor(); ?>; !important;
    	}
	</style>



</head>

<body>
	<!-- Fixed navbar -->
	<div id="head-nav" class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header col-sm-2">
				<a href="/" title="On main">
					<img src="<?php echo Branding::getLogo(); ?>" id="qaLogo" alt="QueryAgent" />
				</a>
			</div>
			<?php
				if (!Yii::app()->user->isGuest && Yii::app()->controller->action->id != "thankyou"):
			?>
				<div class="col-sm-7">
					<div class="col-sm-10">
						<div class="form-group">
							<div class="col-sm-12 form-control rounded searchwrapper">
								<input type="text" class="nCForm" id="searchform" placeholder="<?php echo Yii::t('app', 'Search on quizs, missions, companys, groups of respondents'); ?>">
								<div class="fa fa-search"></div>
								<div id="searchresults">
									<ul>
										<li>213</li>
										<li>123</li>
										<li>123</li>
									</ul>
							</div>
							</div>
						</div>
					</div>
<style type="text/css">
	.course {
		background: url(/images/course_1.png);
		width: 146px;
		height: 53px;
		display: block;
	}

	.course:hover {
		background: url(/images/course_2.png);
	}
</style>
					<div class="col-sm-2" style="padding-top: 10px;"><a href="/site/course" class="course">&nbsp;</a></div>

				</div>

				<div class="col-sm-3 nCUser">
					<div class="nCUser-wrapper nCRight">
					    <div class="col-sm-12">
						    <a href="<?php array('/user/profile/edit') ?>" class="userlink dropdown-toggle" data-toggle="dropdown">
							    <span><?php
								    echo (Yii::app()->user->isGuest ? (Yii::app()->user->name) : (Yii::app()->user->last_name.' '.Yii::app()->user->first_name))
							    ?>
								<br /><?php echo User::model()->findByPk(Yii::app()->user->id)->balance; ?> руб.
							    </span>
							    <?php
							    	$avatar	=	User::model()->findByPk(Yii::app()->user->id)->avatar;
							    	$avatar =	($avatar) ? "/upload/users/".$avatar : "/images/none_avatar.png";
							    ?>
							    <img alt="Avatar" src="<?php echo $avatar; ?>" class="rounded nCImage right">
						    </a>
						    <ul class="dropdown-menu">
	    						<li>
	    							<a href="<?php echo $this->createUrl('/user/profile/edit'); ?>"><?php echo Yii::t('app', 'Edit profile'); ?></a>
							    </li>
	    						<li>
	    							<a href="<?php echo $this->createUrl('/user/profile/password'); ?>"><?php echo Yii::t('app', 'Change password'); ?></a>
							    </li>
	    						<li class="divider"></li>
	    						<li>
	    							<a href="<?php echo $this->createUrl('/user/profile/avatar'); ?>"><?php echo Yii::t('app', 'Change avatar'); ?></a>
							    </li>

							    <?php
							    	$license	=	User::model()->findByPk(Yii::app()->user->id)->license[0];
							    	if ($license->limits->limit_brand_site == Licenses::ACTIVE && $license->active == Licenses::ACTIVE):
							    ?>
								    <li class="divider"></li>
		    						<li>
		    							<a href="<?php echo $this->createUrl('/user/profile/branding'); ?>"><?php echo Yii::t('app', 'Branding'); ?></a>
								    </li>
								<?php
									endif;
								?>

	    						<li class="divider"></li>
	    						<li>
		    						<a href="<?php echo  $this->createUrl('/user/logout'); ?>"><?php echo Yii::app()->getModule('user')->t("Logout"); ?></a>
			    				</li>
				    		</ul>
				    		<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			<?php
				else:
			?>
				<div class="col-sm-3 rounded nCImage right"></div>
			<?php
				endif;
			?>
			<!--/.nav-collapse -->
		</div>
	</div>
	<?php
		if (
			isset($_GET['type']) and $_GET['type'] == "quiz"
		) {
			$hover['quiz'] = "hover";
		}

		if (
			isset($_GET['type']) and $_GET['type'] == "mission"
		) {
			$hover['mission'] = "hover";
		}

		if (
			Yii::app()->controller->id == "client"
		) {
			$hover['client'] = "hover";
		}

		if (
			Yii::app()->controller->id == "groups"
		) {
			$hover['respondent'] = "hover";
		}

		if (
			Yii::app()->controller->id == "finance"
		) {
			$hover['finance'] = "hover";
		}

		#Yii::app()->controller->action->id;
	?>

	<div id="cl-wrapper">
		<?php
			if (!Yii::app()->user->isGuest && Yii::app()->controller->action->id != "thankyou"):
		?>
			<div class="cl-sidebar">
				<div class="cl-toggle">
					<i class="fa fa-bars"></i>
				</div>
				<div class="cl-navblock">
					<div class="menu-space">
						<div class="content">
							<ul class="cl-vnavigation">
								<li class="left-icon interview <?php if(isset($hover['quiz'])) echo $hover['quiz']; ?>">
									<a href="<?php echo $this->createUrl('/quiz'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Quizs'); ?></span>
										<?php
											if (Yii::app()->getModule('user')->isAdmin()) {
												$count	=	Quiz::model()->count("state = :state AND type = :type", array("state" => Quiz::STATE_MODERATION, "type" => Quiz::TYPE_GENERAL));
											}
											else {
												$count	=	Quiz::model()->count("manager_id = :id AND type = :type AND state = :state", array(":id" => Yii::app()->user->id, "type" => Quiz::TYPE_GENERAL, "state" => Quiz::STATE_WORK));
											}

											if ($count) {
												echo '<span class="bubble">'.$count.'</span>';
											}
										?>
									</a>
								</li>
								<?if (true){?>
								<li class="left-icon missions <?php if(isset($hover['mission'])) echo $hover['mission']; ?>">
									<a href="<?php echo $this->createUrl('/mission'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Missions'); ?></span>
										<?php
											if (Yii::app()->getModule('user')->isAdmin()) {
												$count	=	Quiz::model()->count("state = :state AND type = :type", array("state" => Quiz::STATE_MODERATION, "type" => Quiz::TYPE_MISSION));
											}
											else {
												$count	=	Quiz::model()->count("manager_id = :id AND type = :type AND state = :state", array(":id" => Yii::app()->user->id, "type" => Quiz::TYPE_MISSION, "state" => Quiz::STATE_WORK));
											}

											if ($count) {
												echo '<span class="bubble">'.$count.'</span>';
											}
										?>
									</a>
								</li>
								<?}?>
								<li class="left-icon companies <?php if(isset($hover['client'])) echo $hover['client']; ?>">
									<a href="<?php echo $this->createUrl('/user/client'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Clients'); ?></span>
									</a>
								</li>
								<?php
									if (!Yii::app()->getModule('user')->isAdmin()) {
								?>
								<li class="left-icon respondents <?php if(isset($hover['respondent'])) echo $hover['respondent']; ?>">
									<a href="<?php echo $this->createUrl('/respondent/groups'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Respondents<br>groups'); ?></span>
									</a>
								</li>
								<?php
									}
									else if (Yii::app()->getModule('user')->isAdmin()) {
								?>
								<li class="left-icon respondents <?php if(isset($hover['respondent'])) echo $hover['respondent']; ?>">
									<a href="<?php echo $this->createUrl('/respondent'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Respondents'); ?></span>
									</a>
								</li>
								<?php
									}

									if (
										Yii::app()->getModule('user')->isManager()
										&&
										!Yii::app()->getModule('user')->isAdmin()
										&&
										!User::model()->findByPk(Yii::app()->user->id)->subfor
									) {
								?>
								<li class="left-icon finance <?php if(isset($hover['finance'])) $hover['finance']; ?>">
									<div class="bootstro" data-bootstro-step="4" data-bootstro-placement="right" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 4'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 4'); ?>">
										<a href="<?php echo $this->createUrl('/user/finance'); ?>">
											<span class="descr"><?php echo Yii::t('app', 'Finance'); ?></span>
										</a>
									</div>
								</li>
								<?php
								}
								if (Yii::app()->getModule('user')->isAdmin()) {
								?>
								<li class="left-icon licenses">
									<a href="<?php echo $this->createUrl('/licenses'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Licences'); ?></span>
									</a>
								</li>
								<li class="left-icon users">
									<a href="<?php echo $this->createUrl('/user'); ?>">
										<span class="descr"><?php echo Yii::t('app', 'Users'); ?></span>
									</a>
								</li>

								<?php
								}
								?>
								<li class="left-icon dictionaries">
									<div class="bootstro" data-bootstro-step="5" data-bootstro-width="700px" data-bootstro-placement="right" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 5'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 5'); ?>">
										<a href="<?php echo $this->createUrl('/catalog'); ?>">
											<span class="descr"><?php echo Yii::t('app', 'Templates'); ?></span>
										</a>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		<?php
			endif;
		?>

		<div class="container-fluid" id="pcont" >
			<div class="cl-mcont">
	            <?php if(!Yii::app()->user->isGuest && Yii::app()->controller->action->id != "thankyou"): ?>
	                <?php if(isset($this->breadcrumbs)):?>
						<div class="fuelux">
							<div class="wizard">
								<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array('links'=>$this->breadcrumbs)); ?>
							</div>
						</div>
						<!-- breadcrumbs -->
	                <?php endif?>
	            <?php endif; ?>
	            <?php echo $content; ?>
	         </div>
		</div>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nanoscroller/jquery.nanoscroller.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.sparkline/jquery.sparkline.min.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.easypiechart/jquery.easy-pie-chart.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.ui/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.nestable/jquery.nestable.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.switch/bootstrap-switch.min.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.select2/select2.min.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/bootstrap.slider/js/bootstrap-slider.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.gritter/js/jquery.gritter.min.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/behaviour/general.js"></script>
		<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/ecode.js"></script>
		<script>
            (function () {
                App.init();
            })(window.jQuery);

			$(document).ready(function() {
                $(document).on('click', ".dropdown-menu li", function(){
                    $(this).parent().parent().find('.btn').not('.dropdown-toggle').html($(this).text());
                });
			});
        </script>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-48595160-1', 'auto', {
	'allowLinker': true
  });
  ga('send', 'pageview');

</script>

<!-- Yandex.Metrika counter-->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter23332327 = new Ya.Metrika({id:23332327,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    trackHash:true});
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
<noscript><div><img src="//mc.yandex.ru/watch/23332327" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->


<script type="text/javascript">
var reformalOptions = {
project_id: 765362,
project_host: "feedback.queryagent.ru",
tab_orientation: "right",
tab_indent: "50%",
tab_bg_color: "#44a5c5",
tab_border_color: "#FFFFFF",
tab_image_url: "http://tab.reformal.ru/T9GC0LfRi9Cy0Ysg0Lgg0L%252FRgNC10LTQu9C%252B0LbQtdC90LjRjw==/FFFFFF/a08a7c60392f68cb33f77d4f56cf8c6f/right/1/tab.png",
tab_border_width: 0
};

(function() {
var script = document.createElement('script');
script.type = 'text/javascript'; script.async = true;
script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v3/reformal.js';
document.getElementsByTagName('head')[0].appendChild(script);
})();
</script><noscript><a href="http://reformal.ru"><img src="http://media.reformal.ru/reformal.png" /></a><a href="http://feedback.queryagent.ru">Oтзывы и предложения для Query Agent</a></noscript>
</body>

</html>
