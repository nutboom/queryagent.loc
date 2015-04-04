<?php if($questionsArray): ?>
<div class="step-pane group-border-dashed form-horizontal">
        <div class="col-sm-12">
        <?php foreach ($questionsArray as $q => $appQuestion): ?>
            <div class="form-group col-sm-12">
                    <div><?php echo array_search($appQuestion, $questionsArray) + 1; ?>.&nbsp;<?php echo $appQuestion->text; ?></div><br/>

                    <?php if(isset($appQuestion->pictures) && $appQuestion->pictures): ?>
                        <div>
                            <?php foreach ($appQuestion->pictures as $p => $image): ?>
                                <?php echo CHtml::image(QuestionMedia::getPath().'/'.$image['link'], $image['link'], array('class'=>'img-polaroid')) ?>
                            <?php endforeach; ?>
                        </div><br/>
                    <?php endif; ?>

                    <div>
                        <strong><?php echo Yii::t('app', 'Answers'); ?></strong>
                        <?php if($appQuestion->answers): ?>
                            <ol>
                                <?php if(count($appQuestion->respondentAnswer) == 1): ?>
                                    <?php foreach ($appQuestion->answers as $a => $answer): ?>
                                        <?php if($appQuestion->respondentAnswer[0] && $appQuestion->respondentAnswer[0]->id == $answer['id']): ?>
                                            <li class="text-info" title="<?php echo Yii::t('app', 'Respondent answer'); ?>">
                                        <?php else: ?>
                                            <li>
                                        <?php endif; ?>
                                            <?php echo $answer->text; ?>

                                            <?php if(isset($appQuestion['type']) && $appQuestion['type'] == Question::TYPE_SCALE_CLOSE): ?>
                                                <?php $this->widget('bootstrap.widgets.TbBadge', array(
                                                    'label'=>$answer['orderby'],
                                                )); ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($appQuestion->answers as $a => $answer): ?>
                                        <?php $choise = 0; ?>
                                        <?php if($appQuestion->respondentAnswer): ?>
                                            <?php foreach ($appQuestion->respondentAnswer as $ar => $answerResp): ?>
                                                <?php if($answer['id'] == $answerResp['id']): ?>
                                                    <?php $choise = 1; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <?php if($choise): ?>
                                            <li class="text-info" title="<?php echo Yii::t('app', 'Respondent answer'); ?>">
                                        <?php else: ?>
                                            <li>
                                        <?php endif; ?>
                                        <?php echo $answer->text; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ol>
                            <?php if(isset($appQuestion->respondentAnswer[0]->answer_text) && $appQuestion->respondentAnswer[0]->answer_text): ?>
                                <dl class="dl-horizontal">
                                    <dt><?php echo Yii::t('app', 'Respondent answer'); ?>:</dt>
                                    <dd><?php echo $appQuestion->respondentAnswer[0]->answer_text; ?></dd>
                                </dl>
                            <?php endif; ?>
                        <?php elseif($appQuestion->scaled_size > 0): ?>
                            <?php if($appQuestion->respondentAnswer[0]): ?>
                                <?php $this->widget('bootstrap.widgets.TbProgress', array(
                                    'type'=>'info',
                                    'percent'=>($appQuestion->respondentAnswer[0]->answer_text * 100)/ $appQuestion->scaled_size,
                                    'htmlOptions'=>array('title'=>Yii::t('app', 'Scaled Size').': '.$appQuestion->respondentAnswer[0]->answer_text.'   '.Yii::t('app', 'Max scaled size').': '.$appQuestion->scaled_size, 'rel'=>"tooltip"),
                                )); ?>
                            <?php else: ?>
                                <p><em><?php echo Yii::t('app', 'No answer'); ?></em></p>
                            <?php endif; ?>
                        <?php elseif($appQuestion['type'] == Question::TYPE_OPEN): ?>
                            <?php if(isset($appQuestion->respondentAnswer[0]->answer_text) && $appQuestion->respondentAnswer[0]->answer_text): ?>
                                <dl class="dl-horizontal">
                                    <dd><?php echo $appQuestion->respondentAnswer[0]->answer_text; ?></dd>
                                </dl>
                            <?php else: ?>
                                <p><em><?php echo Yii::t('app', 'No answer'); ?></em></p>
                            <?php endif; ?>
                        <?php elseif($appQuestion['type'] == Question::TYPE_ANSWPHOTO): ?>
                            <?php if(isset($appQuestion->respondentAnswer[0]->answer_text) && $appQuestion->respondentAnswer[0]->answer_text): ?>
                                <ul class="unstyled">
                                    <li><?php echo CHtml::image(Application::getPath().basename($appQuestion->respondentAnswer[0]->answer_text), $appQuestion->respondentAnswer[0]->answer_text, array('class'=>'img-polaroid')); ?></li>
                                </ul>
                            <?php else: ?>
                                <p><em><?php echo Yii::t('app', 'No answer'); ?></em></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php else: ?>
    <p class="warning"><?php echo Yii::t('app', 'No'); ?></p>
<?php endif; ?>