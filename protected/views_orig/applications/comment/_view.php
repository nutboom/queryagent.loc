<?php $this->renderPartial('comment/_form',array(
    'quiz'=>$quiz,
    'application'=>$model,
    'comment'=>$comment,
)); ?>

<?php if($model->commentCount >= 1): ?>
    <h3><?php echo Yii::t('app', 'Comments application'); ?></h3>

    <?php $this->widget('bootstrap.widgets.TbGridView', array(
        'type'=>'striped bordered condensed',
        'id'=>'application-comment-grid',
        'dataProvider'=>new CArrayDataProvider($comments),
        'template'=>"{items}\n{pager}",
        'pagerCssClass'=>'pagination pagination-right',
        'columns'=>array(
            array(
                'header'=>ApplicationComment::model()->getAttributeLabel('date_created'),
                'name'=>'date_created',
                'value'=>'Utils::unpack_datetime($data->date_created)',
            ),
            array(
                'header'=>ApplicationComment::model()->getAttributeLabel('text'),
                'name'=>'text',
            ),
            array(
                'header'=>ApplicationComment::model()->getAttributeLabel('role'),
                'name'=>'role',
                'value'=>'ApplicationComment::itemAlias("RoleSender", "$data->role")',
            ),
        ),
    )); ?>
<?php endif; ?>