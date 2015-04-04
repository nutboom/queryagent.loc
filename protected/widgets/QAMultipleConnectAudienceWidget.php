<?php
/**
* QAMultipleConnectAudienceWidget class file.
*
* Dиджет для вывода multiple connect
*
*/
class QAMultipleConnectAudienceWidget extends CWidget
{
    public $scriptFile = 'assets/js/targetAudience.js';

    public function init()
    {
        $this->registerClientScript();
    }

    public function run()
    {
        CWidget::render('qa_multiple', array('questionsArray'=>$questionsArray));
    }

    /**
     * Registers necessary client scripts.
     */
    protected function registerClientScript()
    {
        Yii::app()->getClientScript()->registerScriptFile($this->scriptFile);
    }
}

?>