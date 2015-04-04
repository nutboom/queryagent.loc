<?php
Yii::app()->clientScript->registerScriptFile("/js/amcharts/amcharts.js");
Yii::app()->clientScript->registerScriptFile("/js/amcharts/serial.js");

$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($type).'s') . ($archive ? (' / '.Yii::t('app', 'Archive')) : '');
if($archive)
    $this->breadcrumbs=array(
            Yii::t('app', ucfirst($type).'s')=>array('/'.$type),
            Yii::t('app', 'Archive')
    );
else
    $this->breadcrumbs=array(
            Yii::t('app', ucfirst($type).'s'),
    );

$this->menu=array(
	array('label'=>Yii::t('app', 'Moderation State'),'url'=>array('/'.$type.'/moderation'), 'visible'=>Yii::app()->getModule('user')->isAdmin()),
	array('label'=>Yii::t('app', 'Create '.$type),'url'=>array('/'.$type.'/precreate'), 'visible'=>Yii::app()->getModule('user')->isManager()),
	array('label'=>Yii::t('app', 'Archive'),'url'=>array('/'.$type.'/archive'), 'visible'=>Yii::app()->getModule('user')->isManager() && !$archive),
);
?>

<div class="nCRight">
	<form action="<?php echo $this->createUrl('/'.$type); ?>" method="get">
		<?php
			if($manager){
				echo Yii::t('app', 'User').' <b>'.$manager->username.'</b>&nbsp;&nbsp;';
			}
		?>
		<select name="client" onChange="this.form.submit()">
			<option value=""><?php echo Yii::t('app', 'All clients'); ?></option>
			<?php
				$clients = User::model()->findByPk(Yii::app()->user->id)->client;
				foreach($clients as $id => $client) {
					$selected = "";
					if (isset($_GET['client']) && $_GET['client'] == $id) {
						$selected = "selected";
					}

					echo '<option value="'.$id.'" '.$selected.'>'.$client->name.'</option>';
				}
			?>
		</select>

		<?php
			$selected = array("edit" => "", "moderation" => "", "work" => "", "fill" => "");
			if (isset($_GET['state'])) {
				$selected[$_GET['state']]	=	"selected";
			}
		?>
		<select name="state" onChange="this.form.submit()">
			<option value=""><?php echo Yii::t('app', 'All statuses'); ?></option>
			<option value="edit" <?php echo $selected['edit'] ?>><?php echo Yii::t('app', 'Edit State'); ?></option>
			<?php if(Yii::app()->getModule('user')->isAdmin()): ?>
				<option value="moderation" <?php echo $selected['moderation'] ?>><?php echo Yii::t('app', 'Moderation State'); ?></option>
			<?php endif; ?>
			<option value="work" <?php echo $selected['work'] ?>><?php echo Yii::t('app', 'Work State'); ?></option>
			<option value="fill" <?php echo $selected['fill'] ?>><?php echo Yii::t('app', 'Fill State'); ?></option>
		</select>
	</form>
</div>

<?php
	if ($count):
?>
	<div class="element">
		<div class="pad">
			<?php
			foreach($provider->getData() as $quiz) {
				$this->renderPartial('_list',array(
					'quiz'=>$quiz,
					'type'=>$type,
				));
			}
			?>
		</div>
	</div>

	<div class="pagination">
	<?php
	    $this->widget('CLinkPager', array(
	        'pages' => $pages,
	        'header' => '',
	        'nextPageLabel' => '&rarr;',
	        'prevPageLabel' => '&larr;',
	        'selectedPageCssClass' => 'disabled',
	        'hiddenPageCssClass' => 'disabled',
	        'htmlOptions' => array(
	            'class' => '',
	        )
	    ));
	?>
	</div>
<?php else: ?>
	<?php echo Yii::t('app', 'Now you dont have '.$type, array("{url}"=>$this->createUrl('/'.$type.'/precreate'))); ?>
<?php endif; ?>
