<div class="form well">
    <?php if($isCloseQuestions): ?>
        <ul class="unstyled">
           <?php foreach ($groups as $g => $group): ?>
                <?php if ($group->closeQuestions): ?>
                    <li class="<?php if($answers && $answers[key($answers)]->question->group->id != $group->id): ?> hide<?php endif; ?>">
                        <p><em><?php echo Yii::t('app', 'Group questions'); ?>&nbsp;<?php echo $group->orderby ?></em></p>
                        <ul class="unstyled">
                            <?php foreach ($group->closeQuestions as $q => $question): ?>
                                <li class="box<?php if($answers && $answers[key($answers)]->question->group_id == $question->group_id && $curGroup->condition_question_id != $question->id): ?> hide<?php endif; ?>">
                                    <?php echo CHtml::hiddenField('GroupQuestions['.$order.'][conditions][view]', 1); ?>
                                    <p><?php echo $question->text ?></p>
                                    <?php echo CHtml::checkBoxList('GroupQuestions['.$order.'][conditions]['.$question->id.']', array_keys($answers), CHtml::listData($question->answers, 'id', 'text'),array('class'=>'answers_condition', 'labelOptions'=>array('class'=>'inline_with_checkbox')))?>
                                    <br />
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <?php echo Yii::t('app', 'There are no questions that can affect the display conditions.'); ?>
    <?php endif; ?>
    <div>
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'type'=>'primary',
            'label'=>Yii::t('app', 'Close'),
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
    </div>
</div>