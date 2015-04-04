<?php

/*
 * Utils helpers class
 */
class Utils {
    public static function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////
    // Упаковываем дату и время в MySQL
    public static function pack_datetime($date,$format=1){
            if($format==1)$date=preg_replace("/(\d\d).(\d\d).(\d\d\d\d) (\d\d):(\d\d)/","\\3-\\2-\\1 \\4:\\5:00",$date);
            if($format==4)$date=preg_replace("/(\d\d).(\d\d).(\d\d\d\d) (\d\d):(\d\d):(\d\d)/","\\3-\\2-\\1 \\4:\\5:\\6",$date);
            return $date;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
    // Распаковываем дату и время из MySQL
    public static function unpack_datetime($date,$format=1){
            if($format==1)$date=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/","\\3.\\2.\\1 \\4:\\5",$date);
            if($format==2)$date=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/","\\3.\\2.\\1",$date);
            if($format==3)$date=preg_replace("/(\d\d)(\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/","\\4.\\3.\\2",$date);
            if($format==4)$date=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)/","\\3.\\2.\\1 \\4:\\5:\\6",$date);
            return $date;
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
    // Упаковываем дату в MySQL
    public static function pack_date($date){
            return preg_replace("/(\d\d).(\d\d).(\d\d\d\d)/","\\3-\\2-\\1",$date);
    }
    ////////////////////////////////////////////////////////////////////////////////////////////
    // Распаковываем дату из MySQL
    public static function unpack_date($date,$format=2){
            if($format==2)$date=preg_replace("/(\d\d\d\d)-(\d\d)-(\d\d)/","\\3.\\2.\\1",$date);
            if($format==3)$date=preg_replace("/(\d\d)(\d\d)-(\d\d)-(\d\d)/","\\4.\\3.\\2",$date);
            if($format==4)$date=preg_replace("/(\d\d)(\d\d)-(\d\d)-(\d\d)/","\\4.\\3",$date);
            return $date;
    }

    // Откусывает последние символы. По умолчанию откусывает один символ
    public static function cut_last($string,$num=1){
        return substr($string,0,strlen($string)-$num);
    }

    // Замена в JSON-ответе кирилических букв
    public static function json_encode_cyr($var) {
        $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
        '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
        '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
        '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
        '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
        '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
        '\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
        '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');

        $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
        'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
        'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
        'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');

        if (is_array($var))
        {
            $new = array();
            foreach ($var as $key => $val)
            {
                $new[Utils::json_encode_cyr($key)] = Utils::json_encode_cyr($val);
            }
            $var = $new;
        }
        else if (is_string($var))
        {
            $var = str_replace($arr_replace_utf,$arr_replace_cyr,$var);
        }
        return $var;
    }

    // Преобразование из одной кодировки в другую
    public static function intoCharset($var, $from, $to) {
        if (is_array($var))
        {
            $new = array();
            foreach ($var as $key => $val)
            {
                $new[Utils::intoCharset($key, $from, $to)] = Utils::intoCharset($val, $from, $to);
            }
            $var = $new;
        }
        else if (is_string($var))
        {
            $var = iconv($from, $to, $var);
        }
        return $var;
    }

    /**
    * Отправка почты
    * @param str $to
    * @param str $subject
    * @param str $message
    */
    public static function send_mail($to_addr, $body, $title) {
        $transportType = 'php';
	$viewPath = 'application.views.mail';
        $view = 'layout';
        if(isset(Yii::app()->controller))
                $controller = Yii::app()->controller;
        else
                $controller = new CController('YiiMail');
        $viewPathLayout = Yii::getPathOfAlias($viewPath.'.'.$view).'.php';
        $body['body']['view'] = Yii::getPathOfAlias($viewPath).'/content/'.$body['body']['view'].'.php';

        $message = $controller->renderInternal($viewPathLayout, $body, true);

        Yii::app()->getModule('respondent')->sendMail($to_addr, $title, $message);
    }

    /**
    * Отправка почты
    * @param str $to
    * @param str $subject
    * @param str $message
    */
    public static function send_sms($phone, $body) {
        require_once(Yii::getPathOfAlias("application.components").'/SMS.php');
        return _sms_push($phone, $body);
    }

    /*
    * Создает картинку с графиком
    * @param array $arrValue
    * @param array $legend
    * @param str $image
    */
    public static function chart($arrValue, $legend, $imageName, $pie = false){
        if($pie)
            $pathImg = "Cache/".$imageName."_pie.png";
        else
            $pathImg = "Cache/".$imageName."_chart.png";
        $DataSet = new pData;
        //$DataSet->AddPoint($legend,"Serie2");
        //$DataSet->SetAbsciseLabelSerie("Serie2");

        // Initialise the graph

        // Draw the pie chart
        if($pie){
            $DataSet->AddPoint($arrValue,"Serie1");
            $DataSet->AddAllSeries();

            $Test = new pChart(257,200);
            $Test->setFontProperties("Fonts/tahoma.ttf",8);
            $Test->drawFilledRoundedRectangle(7,7,250,193,5,240,240,240);
            $Test->drawRoundedRectangle(5,5,252,195,5,230,230,230);
            $Test->AntialiasQuality = 0;
            $Test->setShadowProperties(2,2,200,200,200);
            $Test->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),120,100,60,PIE_PERCENTAGE,8);

            $Test->clearShadow();
        }else{
            $count = count($arrValue);
            foreach($arrValue as $k=>$value){
                $serial[$k] = array();
                for($i = 0; $i < $count; $i ++){
                    if($i == $k)
                        $serial[$k][$i] = $value;
                    else
                        $serial[$k][$i] = 0;
                }
            }

            for($i = 0; $i < $count; $i ++){
                $DataSet->AddPoint($serial[$i],"Serie".$i);
            }
            $DataSet->AddAllSeries();

            $Test = new pChart(300,230);
            $Test->setFontProperties("Fonts/tahoma.ttf",8);
            $Test->setGraphArea(50,30,280,200);
            $Test->drawFilledRoundedRectangle(7,7,293,223,5,240,240,240);
            $Test->drawRoundedRectangle(5,5,295,225,5,230,230,230);
            $Test->drawGraphArea(255,255,255,TRUE);
            //$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2,TRUE);
            $Test->drawGrid(4,TRUE,230,230,230,50);

            $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
            $Test->drawGrid(4,TRUE,230,230,230,50);

            // Draw the bar graph
            $Test->drawOverlayBarGraph($DataSet->GetData(),$DataSet->GetDataDescription());
        }

        //$Test->drawPieLegend(200,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

        $Test->Render($pathImg);

        return $pathImg;
    }

    public static function getUniqueSubArrays($arrAddressList)

    {

        foreach ($arrAddressList AS $key => $arrAddress) {

           $arrAddressList[$key] = serialize($arrAddress);

        }

        $arrAddressList = array_unique($arrAddressList);

        foreach ($arrAddressList AS $key => $strAddress) {

            $arrAddressList[$key] = unserialize($strAddress);

        }

        return $arrAddressList;

    }


    public static function getAnswer($id, $answers)

    {

        $result = '';

        foreach ($answers as $answer)

        {

            if ($id == $answer['question_id'])

            {

                if($answer['answer_id'])

                    $result .= $answer['answer_text']."/";

                else

                    $result .= $answer['open_answer'];

            }

        }

        return $result;

    }


    public static function getFullRespondentData($head_arr, $data_arr)

    {

        $result = '';

        foreach ($head_arr as $value)

        {

            $result .= $value.";";

        }

        $result .= "\n";

        foreach ($data_arr as $items)

        {

            foreach ($items as $item)

            {

                $result .= $item.";";

            }

            $result .= "\n";

        }

        return Utils::intoCharset($result, "UTF-8", "windows-1251");

    }
}

?>
