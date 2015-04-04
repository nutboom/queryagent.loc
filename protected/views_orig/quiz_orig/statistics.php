<?php
    $this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($model->type).'s') . ' / '.Yii::t('app','Statistics '.$model->type);
    $this->breadcrumbs=array(
            Yii::t('app', ucfirst($model->type).'s')=>array('/'.$model->type),
            Yii::t('app', 'Statistics '.$model->type),
    );
?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> <!-- 33 KB -->

<link href="/css/fotorama.css" rel="stylesheet">
<script src="/js/fotorama.js"></script>


<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});


    function getChart(obj)
    {
        var chart = obj.val();
        var group = obj.attr("group");

        var func = chart + "" + group;
        window[func]();
    }
</script>

<h2><?php echo Yii::t('app', ucfirst($model->type)); ?>&nbsp;&#171;<?php echo $model->title; ?>&#187;</h2>
<h3><?php echo Yii::t('app', 'Statistics '.$model->type); ?></h3>
<?php if($questions): ?>
    <div class="step-pane group-border-dashed form-horizontal">
        <?php foreach($questions as $q=>$question): ?>
            <div class="form-group">
		<div class="col-sm-12 specPadding">
                    <p><?php echo $q+1; ?>.&nbsp;<?php echo $question['question']->text; ?></p>
                    <?php if(isset($question['question']->pictures) && $question['question']->pictures): ?>
                        <div>
                            <?php foreach ($question['question']->pictures as $p => $image): ?>
                                <?php echo CHtml::image(QuestionMedia::getPath().'/'.$image['link'], $image['link'], array('class'=>'img-polaroid')) ?>
                            <?php endforeach; ?>
                        </div><br/>
                    <?php endif; ?>
                    <?php if(isset($question['question']->answers) && $question['question']->answers): ?>
                        <strong><?php echo Yii::t('app', 'Answers'); ?></strong>
                        <div>
                            <ol>
                                <?php foreach ($question['question']->answers as $a => $answer): ?>
                                    <li>
                                        <span>&nbsp;</span>
                                        <?php echo $answer->text; ?>
                                    </li>
                                <?php endforeach; ?>
                                <?php if($question['question']->type == Question::TYPE_SEMICLOSE): ?>
                                    <li>
                                        <?php echo Yii::t('app', 'Respondent answer'); ?>
                                    </li>
                                <?php endif; ?>
                            </ol>
                        </div><br/>
                    <?php endif; ?>
                    <ul class="unstyled<?php if($question['question']->type != Question::TYPE_SCALE_SCORE): ?> thumbnails<?php endif; ?>">
                        <?php foreach($question['answers'] as $aud=>$audience): ?>
                            <?php if(array_filter(array_values($audience))): ?>
                                <li<?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?> class="stat-audience-scale-score"<?php endif; ?>>
                                    <div<?php if($question['question']->type != Question::TYPE_SCALE_SCORE): ?> class="thumbnail"<?php endif; ?>>
                                        <?php if($aud == end(array_keys($question['answers']))): ?>
                                            <?php echo Yii::t('app', 'Total statistics'); ?>
                                        <?php else: ?>
                                            <?php echo Yii::t('app', 'Target audience'); ?>&nbsp;№<?php echo $aud+1; ?>
                                        <?php endif; ?>

                                        <?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?>
                                            <?php $this->widget('bootstrap.widgets.TbBadge', array(
                                                'type'=>'success', // 'success', 'warning', 'important', 'info' or 'inverse'
                                                'label'=>$audience['other'].'%',
                                            )); ?>
                                        <?php endif; ?>

                                        <?php if($question['question']->type == Question::TYPE_SCALE_SCORE): ?>
                                            <?php $this->widget('bootstrap.widgets.TbProgress', array(
                                                'type'=>'info', // 'info', 'success' or 'danger'
                                                'percent'=>$audience['other'], // the progress
                                                'htmlOptions'=>array('class'=>'col-sm-9', 'title'=>$audience['other'].'%', 'rel'=>"tooltip"),
                                            )); ?>
                                        <?php elseif($question['question']->type == Question::TYPE_ANSWPHOTO): ?>
                                        	<div class="fotorama" data-width="700" data-ratio="700/467" data-max-width="100%" data-nav="thumbs">
		                                        <?php foreach($audience as $photo): ?>
		                                        	<?php echo CHtml::image(Application::getPath().basename($photo), $photo); ?>
		                                        <?php endforeach; ?>
	                                        </div>

                                       <?php else: ?>
                                        	<br />
                       						<?php
	                       						Echo CHtml::dropDownList(
	                       							'chart_type',
	                       							"",
	              									array('PieChart' => 'Пирог', 'ComboChart' => 'График', 'BarChart' => 'Бар'),
	              									array('class' => 'chart_type', 'group' => "{$audience['group']}", 'onChange'=>'getChart($(this));')
	           									);

	           									# Здесь формируем массивы с данными
	           									$cData['pie']	=	"";
	           									foreach ($question['question']->answers as $a => $answer) {
													$cData['pie'] .= "['".$answer->text."', ".$audience[$answer->id]."],";
						                       	}
						                       	$cData['graph']	=	"['',";
						                       	foreach ($question['question']->answers as $a => $answer) {
						                       		$cData['graph']	.=	"'{$answer->text}',";
						       					}
						       					$cData['graph']	.=	"],['',";
											    foreach ($question['question']->answers as $a => $answer) {
											    	$cData['graph']	.=	"{$audience[$answer->id]},";
												}
												$cData['graph']	.=	"],";
             								?>

											<script type="text/javascript">
												google.setOnLoadCallback(PieChart<?php echo $audience['group'] ?>);
												function PieChart<?php echo $audience['group'] ?>() {
													var data = google.visualization.arrayToDataTable([
														['Ответ', 'Доля'],
														<?php echo $cData['pie']; ?>
													]);

													var options = {};

													var chart = new google.visualization.PieChart(document.getElementById('<?php echo $audience['group'] ?>'));
													chart.draw(data, options);
												}

												function ComboChart<?php echo $audience['group'] ?>() {
											        var data = google.visualization.arrayToDataTable([
											          <?php echo $cData['graph']; ?>
											        ]);

											        var options = {
											          seriesType: "bars"
											        };

											        var chart = new google.visualization.ComboChart(document.getElementById('<?php echo $audience['group'] ?>'));
											        chart.draw(data, options);
												}

												function BarChart<?php echo $audience['group'] ?>() {
											        var data = google.visualization.arrayToDataTable([
											          <?php echo $cData['graph']; ?>
											        ]);

											        var options = {
											          seriesType: "bars"
											        };

											        var chart = new google.visualization.BarChart(document.getElementById('<?php echo $audience['group'] ?>'));
											        chart.draw(data, options);
												}
											</script>

											<div id="<?php echo $audience['group'] ?>" style="width: 800px; height: 600px;">Идет загрузка графиков статистики</div>

                                            <?php
                                            	/*
		                                            <?php echo $this->renderPartial('_tab_chart', array(
		                                                'chart'=>CHtml::image('/'.  $audience['chart'], Yii::t('app', 'Statistics question').' '.Yii::t('app', 'Target audience').' №'.$aud),
		                                                'pie'=>CHtml::image('/'.  $audience['pie'], Yii::t('app', 'Statistics question').' '.Yii::t('app', 'Target audience').' №'.$aud))
		                                            ); ?>
		                                      	*/
		                                 	?>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
		</div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><?php echo Yii::t('app', 'Not answers'); ?></p>
<?php endif; ?>
