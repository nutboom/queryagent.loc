<?php Yii::app()->clientScript->registerScript('changed', "
    if($('#User_superuser').val() != 0 || $('#User_manager').val() != 0)
            $('#User_client').parents('.control-group').hide();

    if($('#User_manager').val() == 0)
            $('label[for=User_marketer]').parents('.control-group').hide();

    $('#User_superuser,#User_manager').change(function(){
        if($('#User_superuser').val() == 0 && $('#User_manager').val() == 0){
            $('#User_client').parents('.control-group').show();
            $('label[for=User_marketer] > input[type=checkbox]').attr('checked',false);
            $('label[for=User_marketer]').parents('.control-group').hide();
        }else{
            if($('#User_manager').val() != 0)
                $('label[for=User_marketer]').parents('.control-group').show();
            $('#User_client').parents('.control-group').hide();
        }
        return;
    });
");
?>

<?php
	$attributes = $model->attributeLabels();
	$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		'id'=>'user-form',
		'type'=>'horizontal',
		'htmlOptions'=>array('class'=>'group-border-dashed'),
		'enableAjaxValidation'=>false,
	));
?>
	<div class="step-pane">
		<div class="no-padding nCForm-group">
			<div class="col-sm-7">
				<h3 class="hthin"><?php echo $action; ?></h3>
			</div>
			<div class="clearfix"></div>
		</div>

		<div class="col-sm-12">
			<div class="nCRight"><span class="red">*</span><?php echo Yii::t('app', 'Required fields'); ?></div>
		</div>

		<?php echo $form->errorSummary(array($model,$profile)); ?>


		<?php echo $form->textFieldRow($model,'username',array('class'=>'form-control','size'=>20,'maxlength'=>20)); ?>
		<?php if ($model->isNewRecord): ?>
			<?php echo $form->passwordFieldRow($model,'password',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>
        <?php else: ?>
			<?php echo $form->passwordFieldRow($model,'npassword',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>
        <?php endif; ?>

		<?php echo $form->textFieldRow($model,'email',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->textFieldRow($model,'balance',array('class'=>'form-control','size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->dropDownListRow($model,'superuser',User::itemAlias('AdminStatus')); ?>
		<?php echo $form->dropDownListRow($model,'manager',User::itemAlias('ManagerStatus')); ?>

		<div class="form-group">
			<div class="col-sm-2"></div>
			<div class="col-sm-8">
				<div class="radio green">
					<?php echo CHtml::hiddenField('User[marketer]', '0'); ?>
					<label>
						<?php echo CHtml::checkBox('User[marketer]', $model->marketer, array('class'=>'icheck right')); ?>
						<?php echo $attributes['marketer']; ?>
					</label>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>

		<?php #echo $form->checkBoxRow($model,'marketer'); ?>
		<?php echo $form->dropDownListRow($model,'client',CHtml::listData(Client::model()->findAll(), 'id', 'name'), array('empty' => '')); ?>
		<?php echo $form->dropDownListRow($model,'status',User::itemAlias('UserStatus')); ?>
		<?php
			$profileFields=$profile->getFields();
			if ($profileFields) {
				foreach($profileFields as $field) {
				?>
					<div class="row">
                       	<?php
                       		if ($widgetEdit = $field->widgetEdit($profile)) {
                       			echo $widgetEdit;
                       		} elseif ($field->range) {
                       			echo $form->dropDownListRow($profile,$field->varname,Profile::range($field->range));
                       		} elseif ($field->field_type=="TEXT") {
                       			echo $form->textAreaRow($profile,$field->varname,array('class'=>'form-control','rows'=>6, 'cols'=>50));
                       		} else {
                       			echo $form->textFieldRow($profile,$field->varname,array('class'=>'form-control','size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
                       		}
						?>
						<?php echo $form->error($profile,$field->varname); ?>
					</div>
				<?php
				}
			}
		?>


		<div class="form-group">
			<div class="col-sm-2">
				<?php $this->widget('bootstrap.widgets.TbButton', array(
					'buttonType'=>'submit',
					'type'=>'primary',
					'label'=>$model->isNewRecord ? Yii::t('app','Create') : Yii::t('app','Save'),
				)); ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>