<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>$attr.'Modal')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><?php echo RespondentModule::t($name); ?></h4>
    </div>

    <div class="modal-body offset1">
        <?php if($list): ?>
            <?php echo CHtml::checkBoxList('GroupRespondents['.$attr.']',array_keys($model->$attr),$list,array(
                'separator'=>'',
                'container'=>'',
                'template'=>'<label class="checkbox">{input} {label}</label>',
                'labelOptions'=>array('class'=>'inline_with_checkbox'),
                'class'=>'GroupRespondents_elements'
            )); ?>
        <?php else: ?>
            <blockquote><p class="lead"><?php echo Yii::t('app','Not elements'); ?></p></blockquote>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'type'=>'primary',
            'label'=>Yii::t('app','Save'),
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>

    </div>

<?php $this->endWidget(); ?>
