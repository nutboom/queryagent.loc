<?php
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($type).'s') . ' / '.Yii::t('app','Create');
?>

<div class="col-sm-6 newInterview">
	<div class="no-padding nCForm-group normalLH">
		<div class="col-sm-7">
			<h3 class="hthin">
				<?php
					echo ($type == "quiz") ? Yii::t('app', "Create new interview") : Yii::t('app', "Create new mission");
				?>
			</h3>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-sm-4">
			<div class="bigimage minHeight">
				<img src="/images/new-interview.png" />
			</div>
		</div>
		<div class="col-sm-8">
			<div class="col-sm-12 minHeight120">
				<p>
					<?php
						echo ($type == "quiz") ? Yii::t('app', "Create an absolutely new interview") : Yii::t('app', "Create an absolutely new mission");
					?>
				</p>
			</div>
			<div class="col-sm-12"><a href="/<?php echo $type; ?>/create"><button class="btn btn-danger"><?php echo Yii::t('app', "Continue"); ?></button></a></div>
		</div>

		<div class="clearfix"></div>
	</div>
</div>

<div class="col-sm-6 newInterview copyInterview">
	<div class="no-padding nCForm-group normalLH">
		<div class="col-sm-11">
			<h3 class="hthin">
				<?php
					echo ($type == "quiz") ? Yii::t('app', "Copy existing interview") : Yii::t('app', "Copy existing mission");
				?>
			</h3>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="form-group">
		<div class="col-sm-4">
			<div class="bigimage minHeight">
				<img src="/images/copy-interview.png" />
			</div>
		</div>
		<div class="col-sm-8">
			<div class="col-sm-12 minHeight120">
				<p>
					<?php
						echo ($type == "quiz") ? Yii::t('app', "Create a new interview on base of your previous interviews") : Yii::t('app', "Create a new mission on base of your previous missions");
					?>
				</p>
			</div>
			<div class="col-sm-12"><button class="btn btn-danger" data-toggle='modal' data-target='#myModal'><?php echo Yii::t('app', "Continue"); ?></button></a></div>
		</div>

		<div class="clearfix"></div>
	</div>
</div>


<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>
	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
		<h4><?php echo Yii::t('app',"Choose For Clone ".$type); ?></h4>
	</div>

	<?php
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'=>'quiz-form',
			'type'=>'horizontal',
			'action'=>array('/'.$type.'/clone'),
		));
	?>
	<div class="modal-body offset1">
		<?php
			$quizs = Quiz::model()->findAll("manager_id = :manager_id AND type = :type AND is_deleted = :deleted", array(":manager_id" => Yii::app()->user->id, ":type"=>$type, ":deleted"=>Quiz::NO_DELETED));

			if(count($quizs) > 0) {
				echo CHtml::radioButtonList("quiz", '', CHtml::listData($quizs, 'quiz_id', 'title'), array('class'=>'Quiz_elements'));
			}
			else {
				echo Yii::t('app',"No for clone ".$type);
			}
		?>
	</div>

	<div class="modal-footer">
		<?php
			if(count($quizs) > 0) {
				$this->widget('bootstrap.widgets.TbButton', array(
				'type'=>'primary',
				'buttonType'=>'submit',
				'label'=>Yii::t('app','Next'),
				));
			}
		?>
	</div>
	<?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>