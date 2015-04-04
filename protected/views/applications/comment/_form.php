<?php if($quiz->needs_confirmation && ($application->state == Application::STATE_DONE || $application->is_appeal)): ?>
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'application-comment-Form',
            'enableAjaxValidation'=>false,
            'type'=>'horizontal',
            'htmlOptions'=>array('class'=>'well'),
    )); ?>

        <p class="text-success lead"><?php echo Yii::t('app', 'Requires confirmation of the customer'); ?></p>

        <?php echo $form->dropDownListRow($comment, 'state', Application::itemAlias('ConfirmCustomerStatusApplication')); ?>
        <?php echo $form->textAreaRow($comment, 'text', array('class'=>'span8', 'rows'=>5)); ?>

        <div class="form-actions">
            <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'label'=>Yii::t('app', 'Save'))); ?>
        </div>
    <?php $this->endWidget(); ?>
<?php endif; ?>