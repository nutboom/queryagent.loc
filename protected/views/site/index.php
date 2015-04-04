<?php
Yii::app()->clientScript->registerScriptFile("/js/amcharts/amcharts.js");
Yii::app()->clientScript->registerScriptFile("/js/amcharts/serial.js");

$this->pageTitle=Yii::app()->name;
?>
	<script type="text/javascript">
        $(document).ready(function() {
        	if (!$.cookie('tournamente')) {
        		bootstro.start(null, {
        			prevButtonText: "Назад",
        			nextButtonText: "Далее",
        			finishButtonText: "Пропустить тур",
        		});
        		$.cookie('tournamente', '1' {
					expires: 30
				});
        	}
       	});
	</script>
<div id="navigate_buttons">
	<div class="pad">
		<div class="col-sm-3 bootstro" data-bootstro-step="2" data-bootstro-width="400px" data-bootstro-placement="bottom" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 2'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 2'); ?>">
			<a href="<?php echo $this->createUrl('/quiz/precreate'); ?>">
				<div class="navigate-button n-interview">
					<div class="navigate-button-text">
						<span><?php echo Yii::t('app', 'Create quiz'); ?></span>
					</div>
				</div>
			</a>
		</div>
		<?if (false){?>
		<div class="col-sm-3 bootstro" data-bootstro-step="3" data-bootstro-width="400px" data-bootstro-placement="bottom" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 3'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 3'); ?>">
			<a href="<?php echo $this->createUrl('/mission/precreate'); ?>">
				<div class="navigate-button n-mission">
					<div class="navigate-button-text">
						<span><?php echo Yii::t('app', 'Create mission'); ?></span>
					</div>
				</div>
			</a>
		</div>
		<?}?>
		<div class="col-sm-3 bootstro" data-bootstro-step="0" data-bootstro-width="400px" data-bootstro-placement="bottom" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 0'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 0'); ?>">
			<a href="<?php echo $this->createUrl('/user/client/create'); ?>">
				<div class="navigate-button n-company">
					<div class="navigate-button-text">
						<span><?php echo Yii::t('app', 'Create Client'); ?></span>
					</div>
				</div>
			</a>
		</div>
		<div class="col-sm-3 bootstro" data-bootstro-step="1" data-bootstro-width="400px" data-bootstro-placement="bottom" data-bootstro-content="<?php echo Yii::t('app', 'Tour desc 1'); ?>" data-bootstro-title="<?php echo Yii::t('app', 'Tour title 1'); ?>">
			<a href="<?php echo $this->createUrl('/respondent/groups/create'); ?>">
				<div class="navigate-button n-respond">
					<div class="navigate-button-text">
						<span><?php echo Yii::t('app', 'Create Group of Respondents'); ?></span>
					</div>
				</div>
			</a>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div id="content-inside">
	<div class="pad">
		<div class="element">
			<div class="el-header">
				<h2><h2><?php echo Yii::t('app', 'Current quizs'); ?></h2></h2>
			</div>
		</div>

		<div class="element">
			<div class="pad">
				<?php
				if ($quizs->getData()) {
					foreach($quizs->getData() as $quiz) {
						$this->renderPartial('../quiz/_list',array(
							'quiz'=>$quiz,
							'type'=>'quiz',
						));
					}
				}
				else {
					echo Yii::t('app', 'I have not current quizs, create this');
				}
				?>
			</div>
		</div>
	</div>



	<div class="pad">
		<div class="element">
			<div class="el-header">
				<h2><h2><?php echo Yii::t('app', 'Current missions'); ?></h2></h2>
			</div>
		</div>

		<div class="element">
			<div class="pad">
				<?php
				if ($missions->getData()) {
					foreach($missions->getData() as $quiz) {
						$this->renderPartial('../quiz/_list',array(
							'quiz'=>$quiz,
							'type'=>'mission',
						));
					}
				}
				else {
					echo Yii::t('app', 'I have not current missions, create this');
				}
				?>
			</div>
		</div>
	</div>


</div>