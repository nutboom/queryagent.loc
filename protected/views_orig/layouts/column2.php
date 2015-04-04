<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="span-20">
	<div id="sidebar">
	<?php
		$this->widget('bootstrap.widgets.TbMenu', array(
                    'type'=>'pills', // '', 'tabs', 'pills' (or 'list')
                    'stacked'=>false, // whether this is a stacked menu
                    'items'=>$this->menu,
                ));
	?>
	</div><!-- sidebar -->
</div>
<div class="span-23">
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<?php $this->endContent(); ?>