<?php
Yii::app()->getClientScript()->registerScriptFile('/js/targetAudience.js');
$this->pageTitle=Yii::app()->name . ' / '.Yii::t('app', ucfirst($quiz->type).'s') . ' / '.Yii::t('app','Target audience');

$this->breadcrumbs=array(
    Yii::t('app', 'Wizard Updation') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/update'),
    Yii::t('app', 'Wizard Questions') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'),
    Yii::t('app', 'Wizard Audience') => array(array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience'), "active"),
    Yii::t('app', 'Wizard Collection') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/collection'),
    Yii::t('app', 'Wizard Launch') => array('/'.$quiz->type.'/'.$quiz->quiz_id.'/launch'),
);

if ($quiz->state != Quiz::STATE_EDIT) {
    $this->menu=array(
        array('label'=>Yii::t('app', 'Results'),'url'=>array($quiz->type."/".$quiz->quiz_id."/Applications")),
        array('label'=>Yii::t('app', 'Statistics'),'url'=>array($quiz->type."/".$quiz->quiz_id."/statistics")),
        array('label'=>Yii::t('app', 'Comments'),'url'=>array($quiz->type."/".$quiz->quiz_id."/comments")),
        array('label'=>Yii::t('app', 'Unload results quiz'),'url'=>array($quiz->type."/".$quiz->quiz_id."/export")),
    );
}


?>

<h2><?php echo Yii::t('app', ucfirst($quiz->type)); ?>&nbsp;&#171;<?php echo $quiz->title; ?>&#187;</h2>
<h3><?php echo Yii::t('app', 'Target audience'); ?></h3>

    Если Вы хотите провести опрос в социальных сетях или у себя на сайте нажмите кнопку «Далее», где для Вас будет доступна ссылка на опрос.<br>
    Для проведения опроса по своим группам респондентов (база email адресов) или по базам респондентов сервиса, нажмите соответствующую кнопку<br><br>

<?php
if(count($quiz->audience) > 0){
    $this->widget('bootstrap.widgets.TbGridView', array(
        'type'=>'striped bordered condensed',
        'dataProvider'=>$dataProvider,
        'template'=>"{items}\n{pager}",
        'pagerCssClass'=>'pagination pagination-right',
        'columns'=>array(
            array(
                'header'=>Yii::t('app','Age'),
                'value'=>'$data->concatAge()',
            ),
            array(
                'header'=>Yii::t('app','Income'),
                'value'=>'$data->concatIncome()',
            ),
            array(
                'header'=>Yii::t('app','Gender'),
                'value'=>'TargetAudience::itemAlias("GenderAudience",$data->gender)',
            ),
            array(
                'header'=>Yii::t('app','Marital State'),
                'value'=>'TargetAudience::itemAlias("MaritalStateAudience",$data->marital_state)',
            ),
            /*array(
                'header'=>Yii::t('app','Minimal User State'),
                'value'=>'$data->minimalUserState->title',
            ),*/
            array(
                'header'=>Yii::t('app','Count Limit'),
                'value'=>'$data->count_limit',
            ),
            array(
                'header'=>Yii::t('app','Number of respondents'),
                'value'=>array($this,'gridCountRespondentsRow'),
            ),
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'template'=>'{update} {delete}',
                'updateButtonUrl'=>'Yii::app()->createUrl("'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/update/".$data->primaryKey)',
                'deleteButtonUrl'=>'Yii::app()->createUrl("'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/delete/".$data->primaryKey)',
                'htmlOptions'=>array('style'=>'width: 50px'),
            ),
        ),
    ));}
//    elseif($quiz->by_link){
//        echo '<div style="padding: 10px;">Опрос производится по ссылке</div>';
//    }
$omiTotal = array();
$omiTotalLimit = 0;
if(count($omiAud) > 0){

    echo '<h3>Целевая аудитория OMI</h3>';
    echo '<table style="margin-bottom: 20px;" class="items table table-striped table-bordered table-condensed">';
    echo '<thead><tr><th>Возраст</th><th>Пол</th><th>Образование</th><th>Сфера деятельности</th><th>Мат положение семьи</th><th>Размер города</th><th>Регион</th><th>Город</th>
            <th>Ограничение по кол-ву респондентов</th><th>Кол-во респондентов</th>
            <th style="width: 50px"></th></tr></thead>';
    foreach ($omiAud as $item)
    {
        echo '<tr>';
        echo '<td>'.$item->age_from.'-'.$item->age_to.'</td>';
        echo '<td>'.$item->getField('sex').'</td>';
        echo '<td>'.$item->getField('education').'</td>';
        echo '<td>'.$item->getField('jobsphere').'</td>';
        echo '<td>'.$item->getField('evaluation').'</td>';
        echo '<td>'.$item->getField('citysize').'</td>';
        echo '<td>'.$item->getField('region').'</td>';
        echo '<td>'.$item->getField('city').'</td>';
        echo '<td>'.$item->limit.'</td>';
        echo '<td>'.$item->respondents_count.'</td>';
        if($item->limit == 0 || $item->respondents_count==0) $omiTotal[] = $item->respondents_count;
        elseif($item->limit != 0 && $item->respondents_count!=0) $omiTotal[] = min($item->limit,$item->respondents_count);
        echo '<td>
                <a class="update" title="" rel="tooltip" href="'.Yii::app()->createUrl($quiz->type.'/'.$quiz->quiz_id.'/targetAudience/omiedit?aud_id='.$item->id).'" data-original-title="Редактировать">
                    <i class="icon-pencil"></i>
                </a>
                ';
        echo '<a class="delete" title="" rel="tooltip" href="'.Yii::app()->createUrl($quiz->type.'/'.$quiz->quiz_id.'/targetAudience/omidelete?aud_id='.$item->id).'" data-original-title="Удалить"><i class="icon-trash"></i></a></td></tr>';
    }
    echo '</table>';
    foreach($omiTotal as $value) $omiTotalLimit += $value;
}
?>


<?php
    if ($addButton) {
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            'type'=>'primary',
            'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/create#own'),
            'label'=>'Использовать свою базу респондентов',
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            'type'=>'primary',
            'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/useomi'),
            'label'=>count($omiAud)==0?'Использовать базу респондентов OMI':'Использовать новую ЦА на базе респондентов OMI',
            'htmlOptions'=>array(
                //'disabled'=>'disabled'
            )
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            'type'=>'primary',
            'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/targetAudience/create#service'),
            'label'=>'Выбрать респондентов из базы сервиса',
            'htmlOptions'=>array(
                'disabled'=>'disabled'
            )
        ));

    }
?>

<div class="lead muted">
    <div style="margin: 15px;">
        <?if(false/*$this->gridCountRespondentsAll($quiz->quiz_id) != 0*/){?>
        <em><?php echo Yii::t('app','Total respondents'); ?>:&nbsp;<?php echo ($this->gridCountRespondentsAll($quiz->quiz_id)); ?>
        <?php if($omiTotalLimit < 100) echo " Минимальное количество респондентов 100 человек";?>
        </em>
        <br />
        <?}?>
        <?if($omiTotalLimit != 0 && $quiz->state == Quiz::STATE_EDIT){?>
        <em><?php echo Yii::t('app','Total respondents'); ?> OMI:&nbsp;<?php echo ($omiTotalLimit); ?><?php if($omiTotalLimit < 100) echo " <span style='color: rgb(216, 96, 96);
  font-size: 14px;
  font-weight: 400;
  display: inline-block;'>Минимальное количество респондентов 100 человек</span>";?></em><br>
        <em>Стоимость опроса одного респондента OMI: <?=TargetAudience::RESPONDENT_PRICE?> руб.</em>
        <br />
        
    <!--    <em>--><?php //echo Yii::t('app','Total cost of '.$quiz->type); ?><!--:&nbsp;--><?php //echo $this->gridCountRespondentsAll($quiz->quiz_id)*$quiz->money + $omiTotalLimit*165; ?><!-- --><?php //echo Yii::t('app','rub dot'); ?><!--</em>-->
        <em><?php echo Yii::t('app','Total cost of '.$quiz->type); ?> респондентов OMI:&nbsp;<?php echo number_format($omiTotalLimit*TargetAudience::RESPONDENT_PRICE, 2, '.', ' '); ?> <?php echo Yii::t('app','rub dot'); ?></em><br>
        <em>У вас на балансе <?=number_format($user->balance, 2, '.', ' ')?> <?=Yii::t('app','rub dot')?></em>
        <?if ($user->balance < $omiTotalLimit*TargetAudience::RESPONDENT_PRICE){
            $model  =   new Transactions;
            $model->summ = $omiTotalLimit*TargetAudience::RESPONDENT_PRICE - $user->balance;
            $model->user = Yii::app()->user->id;
            $model->status = Transactions::STATUS_CREATED;
            $model->date_open = Date("Y-m-d h:i:s");
            $model->save();
            $md5    =   "queryagent:".$model->summ.":".$model->id.":jUijx1nH14:Shp_1=".$quiz->quiz_id;
            $md5    =   md5($md5);
            ?> Опрос не может быть запущен.
    </div>
        <form action="https://merchant.roboxchange.com/Index.aspx" method="POST">
            <!-- <form action="http://test.robokassa.ru/Index.aspx" method="POST"> -->
        <?php echo CHtml::hiddenField('MrchLogin', "queryagent"); ?>
        <?php echo CHtml::hiddenField('InvId', $model->id); ?>
        <?php echo CHtml::hiddenField('OutSum', $model->summ); ?>
        <?php echo CHtml::hiddenField('SignatureValue', $md5); ?>
        <?php echo CHtml::hiddenField('Desc', 'Informations'); ?>
        <?php echo CHtml::hiddenField('Shp_1', $quiz->quiz_id); ?>
            <input class="btn btn-primary" type="submit" name="yt1" value="Оплатить <?=number_format($model->summ, 2, '.', ' ')?> руб.">
        </form><?}?>
        <?}?>
</div>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'link',
    'type'=>'primary',
    'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/StructureQuiz'),
    'label'=>Yii::t('app','Back'),
)); ?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'buttonType'=>'link',
    'type'=>'primary',
    'url'=>array('/'.$quiz->type.'/'.$quiz->quiz_id.'/collection'),
    'label'=>Yii::t('app','Next'),
)); ?>