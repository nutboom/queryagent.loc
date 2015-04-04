<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app', 'Update '.$model->type);

$this->breadcrumbs=array(
	Yii::t('app', 'Wizard Updation') => array('/'.$model->type.'/'.$model->quiz_id.'/update'),
    Yii::t('app', 'Wizard Questions') => array('/'.$model->type.'/'.$model->quiz_id.'/StructureQuiz'),
	Yii::t('app', 'Wizard Audience') => array('/'.$model->type.'/'.$model->quiz_id.'/targetAudience'),
	Yii::t('app', 'Wizard Collection') => array('/'.$model->type.'/'.$model->quiz_id.'/collection'),
	Yii::t('app', 'Wizard Launch') => array(array('/'.$model->type.'/'.$model->quiz_id.'/launch'), "active"),
);

if ($model->state != Quiz::STATE_EDIT) {
	$this->menu=array(
		array('label'=>Yii::t('app', 'Results'),'url'=>array($model->type."/".$model->quiz_id."/Applications")),
		array('label'=>Yii::t('app', 'Statistics'),'url'=>array($model->type."/".$model->quiz_id."/statistics")),
		array('label'=>Yii::t('app', 'Comments'),'url'=>array($model->type."/".$model->quiz_id."/comments")),
		array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($model->type."/".$model->quiz_id."/export")),
	);
}
?>


<?php
	$attributes = $model->attributeLabels();
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'quiz-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo Yii::t('app', 'Wizard Launch'); ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>


		<?php echo $form->errorSummary($model); ?>

		<?php
			if ($model->state == Quiz::STATE_EDIT) {
				if(count($model->audience) > 0 && $model->countQuestions() > 0 && !$model->by_link) {
					// может запустить
		  			?>
		  				<div class="form-group">
	                        <div style="padding: 15px;">
	                            Нажмите кнопку «Запустить опрос» для того, чтобы его активировать и начать собирать ответы
	                        </div>
                    	</div>
						<input type="hidden" name="Quiz[state]" value="moderation">
						<div class="form-group">
							<div class="col-sm-2">
								<?php $this->widget('bootstrap.widgets.TbButton', array(
									'buttonType'=>'submit',
									'type'=>'primary',
									'label'=>'Запустить опрос',
								)); ?>
							</div>
						</div>
		  			<?php
		  		}
		  		elseif(count($omiAud)>0 && $model->countQuestions() > 0)
                {
                    $omiTotal = array();
                    $omiTotalLimit =0;
                    foreach ($omiAud as $item)
                    {
                        if($item->limit == 0 || $item->respondents_count==0) $omiTotal[] = $item->respondents_count;
                        elseif($item->limit != 0 && $item->respondents_count!=0) $omiTotal[] = min($item->limit,$item->respondents_count);
                    }
                    foreach($omiTotal as $value) $omiTotalLimit += $value;

                    if(($user->balance < $omiTotalLimit*TargetAudience::RESPONDENT_PRICE))
                    {
                        ?>
                        <div class="form-group">
                            <div style="padding: 15px;">
                                Вы не можете запустить опрос. Пожалуйста, пополните баланс на этапе <a href="/quiz/<?=$model->quiz_id?>/targetAudience">Аудитория</a>
                            </div>
                        </div>
                        <?
                    }
                    elseif($omiTotalLimit < 100)
                    {
                        ?>
                        <div class="form-group">
                            <div style="padding: 15px;">
                                Вы не можете запустить опрос. Опрашиваемая аудитория должна быть менее 100 человек.
                            </div>
                        </div>
                    <?
                    }
                    else
                    {
                        ?>
                        <div class="form-group">
                            <div style="padding: 15px;">
                                Нажмите кнопку «Запустить опрос» для того, чтобы его активировать и начать собирать ответы
                            </div>
                        </div>
                        <input type="hidden" name="Quiz[state]" value="moderation">
                        <div class="form-group">
                            <div class="col-sm-2">
                                <?php $this->widget('bootstrap.widgets.TbButton', array(
                                    'buttonType'=>'submit',
                                    'type'=>'primary',
                                    'label'=>'Запустить опрос',
                                )); ?>
                            </div>
                        </div>
                    <?
                    }
                }
                elseif($model->by_link == true && $model->countQuestions() > 0)
                {
                    ?>
                    <div class="form-group">
	                        <div style="padding: 15px;">
	                            Нажмите кнопку «Запустить опрос» для того, чтобы его активировать и начать собирать ответы
	                        </div>
                    	</div>
                    <input type="hidden" name="Quiz[state]" value="work">
                    <div class="form-group">
                        <div class="col-sm-2">
                            <?php $this->widget('bootstrap.widgets.TbButton', array(
                                'buttonType'=>'submit',
                                'type'=>'primary',
                                'label'=>'Запустить опрос',
                            )); ?>
                        </div>
                    </div>
                <?php
                }
		  		else {
					// не получает запустить
					?>
		            	<div class="form-group">
                        <div style="padding: 15px;">
                            Вы не можете запустить опрос потому что нет вопросов и(или) аудитории
                        </div>
                    </div>
					<?php
				}
			}
			else if ($model->state == Quiz::STATE_WORK) {
				// на заполнении
				?>
		        	<div class="form-group">
                    <div style="padding: 15px;">
                        <h3>Поздравляем, Вы запустили опрос!</h3>
                        Следите за ответами респондентов в режиме реального времени на странице <?echo CHtml::link("«Результаты»","Applications")?>,
                        а также получайте красивые графики статистики на странице <?echo CHtml::link("«Статистика»","statistics")?>!
                    </div>
                </div>
				<?php
			}
			else if ($model->state == Quiz::STATE_MODERATION) {
				// на заполнении
				?>
		        	<div class="form-group">
                    <div style="padding: 15px;">
                        <h3>Ваш опрос отправлен на модерацию.</h3>
                        Наш менеджер проверит анкету и подтвердит запуск в течение двух часов (в рабочее время). <br>
                        В случае успешной модерации опроса, Вы получите имейл сообщение с информацией об активации опроса. <br>
                        Следите за ответами респондентов в режиме реального времени на странице <?echo CHtml::link("«Результаты»","Applications")?>,
                        а также получайте красивые графики статистики на странице <?echo CHtml::link("«Статистика»","statistics")?>!
                    </div>
                </div>
				<?php
			}
			else {
				// заполнен и закрыт
				?>
					<div class="form-group">
                    <div style="padding: 15px;">
                        <h3>Опрос успешно заполнен и закрыт.</h3>
                    </div>
                </div>

				<?php
			}
		?>
	</div>
<?php $this->endWidget(); ?>