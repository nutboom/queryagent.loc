<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('dict_scope_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->dict_scope_id),array('view','id'=>$data->dict_scope_id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($data->title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('is_job')); ?>:</b>
	<?php echo CHtml::encode($data->is_job); ?>
	<br />


</div>