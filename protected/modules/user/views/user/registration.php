<?php
Yii::app()->clientScript->registerCssFile("/css/auth-style.css");
Yii::app()->clientScript->registerScriptFile("/js/auth-script.js");

$this->pageTitle=Yii::app()->name . ' - '.UserModule::t("Registration");
$this->breadcrumbs=array(
	UserModule::t("Registration"),
);

// parse landing data
if ($_POST['name']) {
	$explode = Explode(" ", $_POST['name']);

	$_POST['Profile']['first_name']	=	$explode[0];
	$_POST['Profile']['last_name']	=	$explode[1];

}
?>

<style>
table, td, tr {
	border: none !important;
	background: none !important;
	font-size: 16px !important;
}
.form-text {
    font-size: 12px;
    background-color: #ffffff;
    border: 1px solid #b2d4dc;
    color: #555555;
    display: block;
    font-size: 14px;
    height: 34px;
    line-height: 1.42857;
    padding: 6px 12px;
    vertical-align: middle;
    width: 100%;
}

.form-text:focus {
	border: 1px solid #e36974;
}
.redstar {
	color: red;
}

.formerer {
    background: none repeat scroll 0 0 #fff;
    font-family: 'Roboto',sans-serif;
    min-width: 800px;
    padding: 15px 50px;
    font-size: 16px !important;
    font-color: #a7a7a7;
}

h2 {
	border-bottom: 1px solid #4fc1e9;
	padding-bottom: 5px;
	margin-bottom: 20px;
	font-variant: small-caps;
	color: #5b7180;
}

.captcha {
	border: #ececec 1px solid;

}

.buttonsss {
	background: #e36974;
	border-radius: 5px;
	border: 1px #e36974 solid;
	font-variant: small-caps;
	color: #fff;
	padding: 10px 30px;
	font-size: 20px;
}

label {
	font-weight: normal;
}

.tariffs {
	text-align: center;
	padding-left: 130px;
	
}

.tariffs>label {
	float: left;
	margin-right: 30px;
	width: 38%;
	border-bottom: #e36974 5px solid;
	border-bottom-right-radius: 5px;
	border-bottom-left-radius: 5px;
	padding: 15px 10px;
	background: #fafafa;
	min-height: 320px;

}


.tariffs>label:active {

}

.tariffs>label img {
	margin: 15px 0;
}

.tariffs>label .info {
	margin: 15px 0;
}


.tariffs>label .title {
	font-variant: small-caps;
	font-size: 25px;
	margin-bottom: 10px;
	font-weight: bold;
	color: #3e6372;
}

.tariffs>label .cost {
	color: #fa7e74;
	margin: 15px 0;
}

.tariffs>label .cost span {
	font-weight: bold;
	font-size: 26px;
}
</style>

<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h4><?php echo Yii::t('app', "User agreement"); ?></h4>
    </div>

    <div class="modal-body offset1">
<b>Пользовательское соглашение сервиса быстрых маркетинговых исследований «Query Agent»</b>
<br /><br />

1. Общие положения
<br />1.1. ООО «Кейв групп» (далее - Кейв групп) предлагает пользователю сети Интернет (далее – Пользователь) - использовать сервис “Query Agent” (далее - Сервис), доступный по адресу:  www.queryagent.ru на условиях, изложенных в настоящем Пользовательском соглашении (далее — «Соглашение», «ПС»). Соглашение вступает в силу с момента выражения Пользователем согласия с его условиями в порядке, предусмотренном п. 1.3 Соглашения.
<br />1.2. Для использования Сервиса через ЭВМ Пользователь вправе воспользоваться программой для ЭВМ Query Agent, а для использования Сервиса через мобильные устройства – программой «Query Agent» для мобильных устройств (далее – «Программы»). Использование Программ регламентировано Лицензионным соглашением на использование настольного программного обеспечения и Лицензионным соглашением на использование программы «Query Agent» для мобильных устройств соответственно. 
<br />1.3. Пройдя процедуру регистрации, Пользователь считается принявшим условия Соглашения в полном объеме, без всяких оговорок и исключений. В случае несогласия Пользователя с какими-либо из положений Соглашения, Пользователь не в праве использовать сервис Query Agent. В случае если Сервисом были внесены какие-либо изменения в Соглашение в порядке, предусмотренном пунктом 1.3 Соглашения, с которыми Пользователь не согласен, он обязан прекратить использование сервиса.
<br /><br />

2. Регистрация Пользователя. Учетная запись Пользователя
<br />2.1. Для того чтобы воспользоваться Сервисом, Пользователю необходимо пройти процедуру регистрации, в результате которой для Пользователя будет создана уникальная учетная запись.
<br />2.2. Для регистрации Пользователь обязуется предоставить достоверную и полную информацию о себе по вопросам, предлагаемым в форме регистрации, и поддерживать эту информацию в актуальном состоянии. Если Пользователь предоставляет неверную информацию или у Кейв групп есть основания полагать, что предоставленная Пользователем информация неполна или недостоверна, Кейв групп имеет право по своему усмотрению заблокировать либо удалить учетную запись Пользователя и отказать Пользователю в использовании своего сервиса.
<br />2.3. Кейв групп оставляет за собой право в любой момент потребовать от Пользователя подтверждения данных, указанных при регистрации, и запросить в связи с этим подтверждающие документы (в частности - документы, удостоверяющие личность), непредоставление которых, по усмотрению Кейв групп, может быть приравнено к предоставлению недостоверной информации и повлечь последствия, предусмотренные п. 2.2 Соглашения. В случае если данные Пользователя, указанные в предоставленных им документах, не соответствуют данным, указанным при регистрации, а также в случае, когда данные, указанные при регистрации, не позволяют идентифицировать пользователя, Кейв групп вправе отказать Пользователю в доступе к учетной записи и использовании сервиса Кейв групп.
<br />2.4. Персональная информация Пользователя, содержащаяся в учетной записи Пользователя, хранится и обрабатывается Кейв групп в соответствии с условиями Политики конфиденциальности.
<br />2.5. Логин и пароль для доступа к учетной записи Пользователя. При регистрации Пользователь самостоятельно выбирает себе логин (уникальное символьное имя учетной записи Пользователя) и пароль для доступа к учетной записи. Кейв групп вправе запретить использование определенных логинов, а также устанавливать требования к логину и паролю (длина, допустимые символы и т.д.).
<br />2.6. Пользователь самостоятельно несет ответственность за безопасность (устойчивость к угадыванию) выбранного им пароля, а также самостоятельно обеспечивает конфиденциальность своего пароля. Пользователь самостоятельно несет ответственность за все действия (а также их последствия) в рамках или с использованием сервиса Кейв групп под учетной записью Пользователя, включая случаи добровольной передачи Пользователем данных для доступа к учетной записи Пользователя третьим лицам на любых условиях (в том числе по договорам или соглашениям). При этом все действия в рамках или с использованием сервиса Кейв групп под учетной записью Пользователя считаются произведенными самим Пользователем, за исключением случаев, когда Пользователь, в порядке, предусмотренном п. 2.7., уведомил Кейв групп о несанкционированном доступе к сервису Кейв групп с использованием учетной записи Пользователя и/или о любом нарушении (подозрениях о нарушении) конфиденциальности своего пароля.
<br />2.7. Пользователь обязан немедленно уведомить Кейв групп о любом случае несанкционированного (не разрешенного Пользователем) доступа к сервису Кейв групп с использованием учетной записи Пользователя и/или о любом нарушении (подозрениях о нарушении) конфиденциальности своего пароля. В целях безопасности, Пользователь обязан самостоятельно осуществлять безопасное завершение работы под своей учетной записью (кнопка «Выход») по окончании каждой сессии работы с сервиса Кейв групп. Кейв групп не отвечает за возможную потерю или порчу данных, а также другие последствия любого характера, которые могут произойти из-за нарушения Пользователем положений этой части Соглашения.
<br />2.8. Использование Пользователем своей учетной записи. Пользователь не в праве воспроизводить, повторять и копировать, продавать и перепродавать, а также использовать для каких-либо коммерческих целей какие-либо части сервиса Кейв групп (включая контент, доступный Пользователю посредством сервисов), или доступ к ним, кроме тех случаев, когда Пользователь получил такое разрешение от Кейв групп, либо когда это прямо предусмотрено пользовательским соглашением какого-либо сервиса.
<br />2.9. Прекращение регистрации. Кейв групп вправе заблокировать или удалить учетную запись Пользователя, а также запретить доступ с использованием какой-либо учетной записи к  сервису Кейв групп, и удалить любой контент без объяснения причин, в том числе в случае нарушения Пользователем условий Соглашения.
<br />2.10. Удаление учетной записи Пользователя.
<br />2.10.1. Пользователь вправе в любой момент удалить свою учетную запись в сервисе или прекратить ее действие в отношении некоторых из них, воспользовавшись соответствующей функцией в персональном разделе.
<br />2.10.2. Удаление учетной записи Кейв групп осуществляется в следующем порядке:
<br />2.10.2.1. учетная запись блокируется на срок один месяц, в течение которого размещенные с ее использованием контент и иные пользовательские данные не удаляются, однако доступ к ним становится невозможен как для Пользователя – владельца учетной записи, так и для других пользователей;
<br />2.10.2.2. если в течение указанного выше срока учетная запись Пользователя будет восстановлена, доступ к указанным данным возобновляется в объеме, существовавшем на момент блокирования (за исключением контента, нарушающего условия Соглашения или иных документов, регулирующих соответствующий сервис);
<br />2.10.2.3. если в течение указанного выше срока учетная запись Пользователя не будет восстановлена, весь контент, размещенный с ее использованием, будет удален, а логин будет доступен для использования другим пользователям. С этого момента восстановление учетной записи, какой-либо информации, относящейся к ней, а равно доступов к сервису Кейв групп с использованием этой учетной записи - невозможны.
<br /><br />

3. Общие положения об использовании и хранении
<br />3.1. Кейв групп вправе устанавливать ограничения в использовании сервиса для всех Пользователей, либо для отдельных категорий Пользователей (в зависимости от места пребывания Пользователя, языка, на котором предоставляется сервис и т.д.), в том числе: наличие/отсутствие отдельных функций сервиса, срок хранения любого другого контента, максимальное количество сообщений, которые могут быть отправлены или получены одним зарегистрированным пользователем, максимальный размер почтового сообщения или дискового пространства, максимальное количество обращений к сервису за указанный период времени, максимальный срок хранения контента, специальные параметры загружаемого контента и т.д. Кейв групп может запретить автоматическое обращение к своим сервисам, а также прекратить прием любой информации, сгенерированной автоматически (например, почтового спама).
<br />3.2. Кейв групп вправе посылать своим пользователям информационные сообщения.
<br />4. Контент Пользователя
<br />4.1. Пользователь самостоятельно несет ответственность за соответствие содержания размещаемого Пользователем контента требованиям действующего законодательства, включая ответственность перед третьим лицами в случаях, когда размещение Пользователем того или иного контента или содержание контента нарушает права и законные интересы третьих лиц, в том числе личные неимущественные права авторов, иные интеллектуальные права третьих лиц, и/или посягает на принадлежащие им нематериальные блага.
<br />4.2. Пользователь признает и соглашается с тем, что Кейв групп не обязан просматривать контент любого вида, размещаемый и/или распространяемый Пользователем посредством сервиса Кейв групп, а также то, что Кейв групп имеет право (но не обязанность) по своему усмотрению отказать Пользователю в размещении и/или распространении им контента или удалить любой контент, который доступен посредством сервиса Кейв групп. Пользователь осознает и согласен с тем, что он должен самостоятельно оценивать все риски, связанные с использованием контента, включая оценку надежности, полноты или полезности этого контента.
<br />4.3. Пользователь осознает и соглашается с тем, что технология работы сервиса может потребовать копирование (воспроизведение) контента Пользователя Кейв групп, а также переработки его Кейв групп для соответствия техническим требованиям сервиса.
<br /><br />

5. Условия использования сервиса 
<br />5.1. Пользователь самостоятельно несет ответственность перед третьими лицами за свои действия, связанные с использованием Сервиса, в том числе, если такие действия приведут к нарушению прав и законных интересов третьих лиц, а также за соблюдение законодательства при использовании Сервиса.
<br />5.2. При использовании сервиса Кейв групп Пользователь не вправе:
<br />5.2.1. загружать, посылать, передавать или любым другим способом размещать и/или распространять контент, который является незаконным, вредоносным, клеветническим, оскорбляет нравственность, демонстрирует (или является пропагандой) насилия и жестокости, нарушает права интеллектуальной собственности, пропагандирует ненависть и/или дискриминацию людей по расовому, этническому, половому, религиозному, социальному признакам, содержит оскорбления в адрес каких-либо лиц или организаций, содержит элементы (или является пропагандой) порнографии, детской эротики, представляет собой рекламу (или является пропагандой) услуг сексуального характера (в том числе под видом иных услуг), разъясняет порядок изготовления, применения или иного использования наркотических веществ или их аналогов, взрывчатых веществ или иного оружия;
<br />5.2.2. нарушать права третьих лиц, в том числе несовершеннолетних лиц и/или причинять им вред в любой форме;
<br />5.2.3. выдавать себя за другого человека или представителя организации и/или сообщества без достаточных на то прав, в том числе за сотрудников Кейв групп, за модераторов форумов, за владельца сайта, а также применять любые другие формы и способы незаконного представительства других лиц в сети, а также вводить пользователей или Кейв групп в заблуждение относительно свойств и характеристик каких-либо субъектов или объектов;
<br />5.2.4. загружать, посылать, передавать или любым другим способом размещать и/или распространять контент, при отсутствии прав на такие действия согласно законодательству или каким-либо договорным отношениям;
<br />5.2.5. загружать, посылать, передавать или любым другим способом размещать и/или распространять не разрешенную специальным образом рекламную информацию, спам (в том числе и поисковый), списки чужих адресов электронной почты, схемы «пирамид», многоуровневого (сетевого) маркетинга (MLM), системы интернет-заработка и e-mail-бизнесов, «письма счастья», а также использовать сервис Кейв групп для участия в этих мероприятиях, или использовать сервис Кейв групп, исключительно для перенаправления пользователей на страницы других доменов;
<br />5.2.6. загружать, посылать, передавать или любым другим способом размещать и/или распространять какие-либо материалы, содержащие вирусы или другие компьютерные коды, файлы или программы, предназначенные для нарушения, уничтожения либо ограничения функциональности любого компьютерного или телекоммуникационного оборудования или программ, для осуществления несанкционированного доступа, а также серийные номера к коммерческим программным продуктам и программы для их генерации, логины, пароли и прочие средства для получения несанкционированного доступа к платным ресурсам в Интернете, а также размещения ссылок на вышеуказанную информацию;
<br />5.2.7. несанкционированно собирать и хранить персональные данные других лиц;
<br />5.2.8. нарушать нормальную работу веб-сайта и сервиса Кейв групп ;
<br />5.2.9. размещать ссылки на ресурсы сети, содержание которых противоречит действующему законодательству РФ;
<br />5.2.10. содействовать действиям, направленным на нарушение ограничений и запретов, налагаемых Соглашением;
<br />5.2.11. другим образом нарушать нормы законодательства, в том числе нормы международного права.
<br /><br />

6. Исключительные права на содержание сервисов и контент
<br />6.1. Все объекты, доступные при помощи сервиса Кейв групп, в том числе элементы дизайна, текст, графические изображения, иллюстрации, видео, программы для ЭВМ, базы данных и другие объекты (далее – содержание сервисов), а также любой контент, размещенный на сервисе Кейв групп , являются объектами исключительных прав Кейв групп, Пользователей и других правообладателей.
<br />6.2. Использование контента, а также каких-либо иных элементов сервиса возможно только в рамках функционала, предлагаемого сервисом. Никакие элементы содержания сервиса Кейв групп, а также любой контент, размещенный на сервисе Кейв групп, не могут быть использованы иным образом без предварительного разрешения правообладателя. Под использованием подразумеваются, в том числе: воспроизведение, копирование, переработка, распространение на любой основе, отображение во фрейме и т.д. Исключение составляют случаи, прямо предусмотренные законодательством РФ или условиями использования сервиса . Кейв групп.
<br /><br />

Использование Пользователем элементов содержания сервиса, а также любого контента для личного некоммерческого использования, допускается при условии сохранения всех знаков охраны авторского права, смежных прав, товарных знаков, других уведомлений об авторстве, сохранения имени (или псевдонима) автора/наименования правообладателя в неизменном виде, сохранении соответствующего объекта в неизменном виде. Исключение составляют случаи, прямо предусмотренные законодательством РФ или пользовательскими соглашениями сервиса  Кейв групп.
<br />7. Сайты и контент третьих лиц
<br />7.1. Сервис Кейв групп может содержать ссылки на другие сайты в сети Интернет (сайты третьих лиц). Указанные третьи лица и их контент не проверяются Кейв групп на соответствие тем или иным требованиям (достоверности, полноты, законности и т.п.). Кейв групп не несет ответственность за любую информацию, материалы, размещенные на сайтах третьих лиц, к которым Пользователь получает доступ с использованием сервиса, в том числе, за любые мнения или утверждения, выраженные на сайтах третьих лиц, рекламу и т.п., а также за доступность таких сайтов или контента и последствия их использования Пользователем.
<br />7.2. Ссылка (в любой форме) на любой сайт, продукт, услугу, любую информацию коммерческого или некоммерческого характера, размещенная на Сайте, не является одобрением или рекомендацией данных продуктов (услуг, деятельности) со стороны Кейв групп, за исключением случаев, когда на это прямо указывается на ресурсах Кейв групп.
<br />8. Отсутствие гарантий, ограничение ответственности
<br />8.1. Пользователь использует сервис Кейв групп на свой собственный риск. Сервис предоставляются «как есть». Кейв групп не принимает на себя никакой ответственности, в том числе за соответствие сервиса целям Пользователя;
<br />8.2. Кейв групп не гарантирует, что: сервис соответствует/будет соответствовать требованиям Пользователя; сервис будут предоставляться непрерывно, быстро, надежно и без ошибок; результаты, которые могут быть получены с использованием сервиса, будут точными и надежными и могут использоваться для каких-либо целей или в каком-либо качестве (например, для установления и/или подтверждения каких-либо фактов); качество какого-либо продукта, услуги, информации и пр., полученных с использованием сервиса, будет соответствовать ожиданиям Пользователя;
<br />8.3. Любые информацию и/или материалы (в том числе загружаемое ПО, письма, какие-либо инструкции и руководства к действию и т.д.), доступ к которым Пользователь получает с использованием сервиса Кейв групп, Пользователь может использовать на свой собственный страх и риск и самостоятельно несет ответственность за возможные последствия использования указанных информации и/или материалов, в том числе за ущерб, который это может причинить компьютеру Пользователя или третьим лицам, за потерю данных или любой другой вред;
<br />8.4. Кейв групп не несет ответственности за любые виды убытков, произошедшие вследствие использования Пользователем сервиса Кейв групп или отдельных частей/функций сервисов;
<br />8.5. При любых обстоятельствах ответственность Кейв групп в соответствии со статьей 15 Гражданского кодекса России ограничена 10 000 (десятью тысячами) рублей РФ и возлагается на него при наличии в его действиях вины.
<br /><br />

9. Иные положения
<br />9.1. Настоящее Соглашение представляет собой договор между Пользователем и Кейв групп относительно порядка использования сервиса и заменяет собой все предыдущие соглашения между Пользователем и Кейв групп.
<br />9.2. Настоящее Соглашение регулируется и толкуется в соответствии с законодательством Российской Федерации. Вопросы, не урегулированные настоящим Соглашением, подлежат разрешению в соответствии с законодательством Российской Федерации. Все возможные споры, вытекающие из отношений, регулируемых настоящим Соглашением, разрешаются в порядке, установленном действующим законодательством Российской Федерации, по нормам российского права. Везде по тексту настоящего Соглашения, если явно не указано иное, под термином «законодательство» понимается как законодательство Российской Федерации, так и законодательство места пребывания Пользователя.
<br />9.3. Ввиду безвозмездности услуг, оказываемых в рамках настоящего Соглашения, нормы о защите прав потребителей, предусмотренные законодательством Российской Федерации, не могут быть применимыми к отношениям между Пользователем и Кейв групп.
<br />9.4. Ничто в Соглашении не может пониматься как установление между Пользователем и Сервисом агентских отношений, отношений товарищества, отношений по совместной деятельности, отношений личного найма, либо каких-то иных отношений, прямо не предусмотренных Соглашением.
<br />9.5. Если по тем или иным причинам одно или несколько положений настоящего Соглашения будут признаны недействительными или не имеющими юридической силы, это не оказывает влияния на действительность или применимость остальных положений Соглашения.
<br />9.6. Бездействие со стороны Кейв групп в случае нарушения Пользователем либо иными пользователями положений Соглашений не лишает Кейв групп права предпринять соответствующие действия в защиту своих интересов позднее, а также не означает отказа Кейв групп от своих прав в случае совершения в последующем подобных либо сходных нарушений.
<br />9.7. Настоящее Соглашение составлено на русском языке и в некоторых случаях может быть предоставлено Пользователю для ознакомления на другом языке. В случае расхождения русскоязычной версии Соглашения и версии Соглашения на ином языке, применяются положения русскоязычной версии настоящего Соглашения.

    </div>

<?php $this->endWidget(); ?>

<?php if(Yii::app()->user->hasFlash('registration')): ?>
	<div class="success">
		<?php echo Yii::app()->user->getFlash('registration'); ?>
	</div>
<?php else: ?>
	<div class="cl-mcont">
		<div class="col-sm-12">
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
				'id'=>'register-form',
				'type'=>'vertical',
				/*'htmlOptions'=>array('class'=>'well'),*/
				'enableAjaxValidation'=>true,
				'clientOptions'=>array(
					'validateOnSubmit'=>true,
				),
				'htmlOptions' => array('enctype'=>'multipart/form-data', 'class'=>'register-form'),
			)); ?>
			<?php echo $form->errorSummary(array($model,$profile)); ?>

			<div class="formerer">

                <h2><?php echo UserModule::t("Registration"); ?></h2>

				<table style="width: 100%;">
					<tr>
						<td style="width: 200px;"><?php echo UserModule::t("username"); ?> <span class="redstar">*</span></td>
						<td><?php echo $form->textField($model,'username',array('class'=>'form-text','placeholder'=>UserModule::t("username"))); ?></td>
					</tr>

					<tr>
						<td><?php echo Yii::t('app', "Password"); ?> <span class="redstar">*</span></td>
						<td><?php echo $form->passwordField($model,'password',array('class'=>'form-text password','placeholder'=>Yii::t('app', "Password"))); ?></td>
					</tr>


					<tr>
						<td><?php echo Yii::t('app', "Password Retry"); ?> <span class="redstar">*</span></td>
						<td><?php echo $form->passwordField($model,'verifyPassword',array('class'=>'form-text reenter_password','placeholder'=>Yii::t('app', "Password Retry"))); ?></td>
					</tr>


					<tr>
						<td><?php echo Yii::t('app', "Phone number"); ?> <span class="redstar">*</span></td>
						<td><?php echo $form->textField($model,'phone_number',array('class'=>'form-text','placeholder'=>Yii::t('app', 'Phone number'))); ?></td>
					</tr>


					<tr>
						<td><?php echo UserModule::t("E-mail"); ?> <span class="redstar">*</span></td>
						<td><?php echo $form->textField($model,'email',array('class'=>'form-text','placeholder'=>UserModule::t("E-mail"))); ?></td>
					</tr>

					<tr>
						<td><?php echo UserModule::t("First Name"); ?> <span class="redstar">*</span></td>
						<td><input id="Profile_first_name" type="text" name="Profile[first_name]" value="<?php echo $_POST['Profile']['first_name']; ?>" class="form-text" placeholder="<?php echo UserModule::t("First Name"); ?>"></td>
					</tr>

					<tr>
						<td><?php echo UserModule::t("Last Name"); ?> <span class="redstar">*</span></td>
						<td><input id="Profile_last_name" type="text" name="Profile[last_name]" value="<?php echo $_POST['Profile']['last_name']; ?>" class="form-text" placeholder="<?php echo UserModule::t("Last Name"); ?>"></td>
					</tr>
				</table>

				<br /><br />

                <?
                if (false){//убираем выбор тарифов
                ?>
				<h2><?php echo Yii::t('app', 'Chose your tarifny plan'); ?></h2>
                        

                <div class="tariffs">
                	<label>
                		<div class="title">Trial</div>
                		<img src="/images/free.png">
                		<div class="cost"><span>Бесплатно</span></div>
                		<input type="radio" name="Licenses[tariff]" value="1" class="nc_iCheck" checked>
                	</label>

                	<label>
                		<div class="title">Спецпредложение</div>
                		<img src="/images/corporate.png">
                		<div class="cost"><span><?php echo Ceil(Tariffs::model()->findByPk(4)->cost*12); ?> руб.</span> в год</div>
                		<input type="radio" name="Licenses[tariff]" value="4" class="nc_iCheck">
                	</label>
                </div>
                <div class="clearfix"></div>
                <br /><br />

                <?
                }
                ?>

                <span><?php echo Yii::t('app', 'Check register code'); ?></span>
                <table>
                    <tr>
                    	<td style="width: 100px;">
                    		<span class="captcha"><?php $this->widget('CCaptcha'); ?></span>
                        </td>
                        <td><input class="form-text captchaField" id="RegistrationForm_verifyCode" type="text" name="RegistrationForm[verifyCode]" placeholder="<?php echo Yii::t('app', 'Check register code'); ?>" /></td>
                    </tr>                            	
                </table>
                <br /><br />


                <table>
                    <tr>
                    	<td>
                    		<label>
                    			<input class="repCheckbox" type="checkbox"/>
                    			&nbsp;
                            	<?php echo Yii::t('app', 'I agree rules'); ?>
                            </label>
                        </td>
                        <td><button class="buttonsss"><?php echo UserModule::t('Register') ?></button></td>
                    </tr>                            	
                </table>
                <br /><br />



                <div class="clearfix"></div>
                        </div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
<?php endif; ?>