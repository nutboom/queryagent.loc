<?php $this->widget('bootstrap.widgets.TbTabs', array(
    'type'=>'tabs', // 'tabs' or 'pills'
    'tabs'=>array(
        array('label'=>Yii::t('app', 'Diagram').' №1', 'content'=>$chart, 'active'=>true),
        array('label'=>Yii::t('app', 'Diagram').' №2', 'content'=>$pie),
    ),
)); ?>