<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>$attr.'Modal')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><?php echo Yii::t('app',$name); ?></h4>
    </div>

    <div class="modal-body">
        <?php if($list): ?>
            <?php if($attr == 'countries'): ?>
                <ul class="thumbnails">
                    <li class="span4">
                        <h3><?php echo Yii::t('app','Select countries'); ?></h3>
                        <div id="country" class="thumbnail">
                            <?php echo CHtml::checkBoxList('TargetAudience['.$attr.']',array_keys($model->$attr),$list,array(
                                'separator'=>'',
                                'container'=>'',
                                'template'=>'<label class="checkbox">{input} {label}</label>',
                                'labelOptions'=>array('class'=>'inline_with_checkbox'),
                                'class'=>'choose_country'
                                )); ?>
                        </div>
                    </li>
                    <li class="span4">
                        <h3><?php echo Yii::t('app','Select cities'); ?></h3>
                        <div id="city" class="thumbnail">
                            <?php foreach($list as $id=>$el): ?>
                                <div id="cities_country_<?php echo $id; ?>"></div>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
            <?php elseif($attr == 'classfAnswers'): ?>
                <?php $question = ''; ?>
                <dl>
                    <?php foreach ($list as $key => $answer): ?>
                            <?php if($question != $answer->question['id']): ?>
                                <?php if($question != ''): ?>
                                    </dl><dl>
                                <?php endif; ?>
                                <?php $question = $answer->question['id']; ?>
                                <dt>
                                    <?php echo $answer->question['text']; ?>
                                </dt>
                            <?php endif; ?>
                            <dd>
                                <label class="checkbox">
                                    <?php echo CHtml::checkBox('TargetAudience['.$attr.'][]', in_array($key, array_keys($model->$attr)),array(
                                        'id'=>$answer['id'],
                                        'value'=>$answer['id'],
                                        'class'=>'choose_classfquestion'
                                    )); ?>
                                    <?php echo CHtml::label($answer['text'], $answer['id'])?>
                                </label>
                            </dd>
                    <?php endforeach; ?>
                </dl>
            <?php else: ?>
            	<?php if ($attr == 'groupsRespondents'): ?>
	                <?php echo CHtml::checkBoxList('TargetAudience['.$attr.']',array_keys($model->$attr),$list,array(
	                    'separator'=>'',
	                    'container'=>'',
	                    'template'=>'<label class="checkbox">{input} {label}</label>',
	                    'labelOptions'=>array('class'=>'inline_with_checkbox'),
	                    'class'=>'TargetAudience_groups'
	                )); ?>
	               <br />
	               <a href="<?php echo $this->createUrl('/respondent/groups/create', array('quiz' => $model->quiz_id)); ?>"><?php echo Yii::t('app','Create Group of Respondents'); ?></a>
                <?php else: ?>
	                <?php echo CHtml::checkBoxList('TargetAudience['.$attr.']',array_keys($model->$attr),$list,array(
	                    'separator'=>'',
	                    'container'=>'',
	                    'template'=>'<label class="checkbox">{input} {label}</label>',
	                    'labelOptions'=>array('class'=>'inline_with_checkbox'),
	                    'class'=>'TargetAudience_elements'
	                )); ?>
                <?php endif; ?>
             <?php endif; ?>
        <?php else: ?>
            <blockquote><p class="lead"><?php echo Yii::t('app','Not elements'); ?></p></blockquote>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?php
        	$id = '';
        	$disabled = false;
        	if ($attr == 'groupsRespondents') {
        		$id = 'use_group';
        		$disabled = true;
        	}


        	$this->widget('bootstrap.widgets.TbButton', array(
            'type'=>'primary',
            'label'=>Yii::t('app','Save'),
            'url'=>'#',
            'htmlOptions'=>array(
            	'disabled'=>$disabled,
            	'data-dismiss'=>'modal',
            	'id'=> $id
            ),
        )); ?>
    </div>

<?php $this->endWidget(); ?>

<?php if($attr != 'groupsRespondents'): ?>
	<div class="form-group">
		<div class="col-sm-2"></div>
		<div class="col-sm-2">
		    <?php $this->widget('bootstrap.widgets.TbButton', array(
		        'label'=>Yii::t('app',$name),
		        'htmlOptions'=>array(
		            'data-toggle'=>'modal',
		            'data-target'=>'#'.$attr.'Modal',
		            'class'=>'btn btn-primary nCButton',
		        ),
		    )); ?>
	    </div>


	    <div class="modal-content">
	        <?php if($attr != 'countries' && $attr != 'classfAnswers'): ?>
	            <?php foreach ($model->$attr as $id => $elem): ?>
	                <ul id="TargetAudience_<?php echo $attr; ?>_<?php echo ($id-1); ?>"><li><?php echo $elem->title; ?></li></ul>
	            <?php endforeach; ?>
	        <?php endif; ?>
	    </div>
	</div>
<?php endif; ?>