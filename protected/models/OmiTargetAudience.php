<?php

/**
 * This is the model class for table "{{omi_target_audience}}".
 *
 * The followings are the available columns in table '{{omi_target_audience}}':
 * @property integer $id
 * @property integer $quiz_id
 * @property integer $sex
 * @property integer $age_from
 * @property integer $age_to
 * @property string $city
 * @property string $region
 * @property string $citysize
 * @property string $education
 * @property string $jobsphere
 * @property string $evaluation
 * @property string $limit
 */
class OmiTargetAudience extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OmiTargetAudience the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{omi_target_audience}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('quiz_id, sex, age_from, age_to, city, region, citysize, education, jobsphere, evaluation,limit', 'required'),
			array('quiz_id, sex, age_from, age_to', 'numerical', 'integerOnly'=>true),
			array('city, region, citysize, education, jobsphere, evaluation', 'length', 'max'=>400),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, quiz_id, sex, age_from, age_to, city, region, citysize, education, jobsphere, evaluation', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'quiz_id' => 'Quiz',
			'sex' => 'Sex',
			'age_from' => 'Age From',
			'age_to' => 'Age To',
			'city' => 'City',
			'region' => 'Region',
			'citysize' => 'Citysize',
			'education' => 'Education',
			'jobsphere' => 'Jobsphere',
			'evaluation' => 'Evaluation',
			'limit' => 'Limit',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('quiz_id',$this->quiz_id);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('age_from',$this->age_from);
		$criteria->compare('age_to',$this->age_to);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('region',$this->region,true);
		$criteria->compare('citysize',$this->citysize,true);
		$criteria->compare('education',$this->education,true);
		$criteria->compare('jobsphere',$this->jobsphere,true);
		$criteria->compare('evaluation',$this->evaluation,true);
		$criteria->compare('limit',$this->evaluation,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public static function haveOmiAudience($quiz_id)
    {
        $model = self::model()->findByAttributes(array('quiz_id'=>$quiz_id));
        if($model) return true;
        else return false;

    }

    public function getField($field)//получаем не id а слова из связанных таблиц
    {
        $connection=Yii::app()->db;
        switch($field)
        {
            case 'sex':
                $sql = "SELECT title FROM tbl_omi_sex WHERE id = :id";
                $command = $connection->createCommand($sql);
                $command->bindParam(":id",$this->sex);
                $data = $command->query();
                if ($sex = $data->read()) return $sex['title'];
                else return 'Любой';
            case 'education':
                if ($this->education != '')
                {
                    $sql = "SELECT * FROM tbl_omi_education WHERE id IN ($this->education)";
                    $command = $connection->createCommand($sql);
                    $data = $command->query();
                    $result ='';
                    while ($education = $data->read())
                    {
                        $result .= $education['title'].",<br>";
                    }
                    return $result;
                }
                break;

            case 'jobsphere':
                if ($this->jobsphere != '')
                {
                    $sql = "SELECT title FROM tbl_omi_job_sphere WHERE id IN ($this->jobsphere)";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":ids",$this->jobsphere);
                    $data = $command->query();
                    $result ='';
                    while ($jobsphere = $data->read())
                    {
                        $result .= $jobsphere['title'].",<br>";
                    }
                    return $result;
                }
                break;

            case 'evaluation':
                if ($this->evaluation != '')
                {
                    $sql = "SELECT title FROM tbl_omi_income_evaluation WHERE id IN ($this->evaluation)";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":ids",$this->evaluation);
                    $data = $command->query();
                    $result ='';
                    while ($evaluation = $data->read())
                    {
                        $result .= $evaluation['title'].",<br>";
                    }
                    return $result;
                }
                break;

            case 'citysize':
                if ($this->citysize != '')
                {
                    $sql = "SELECT title FROM tbl_omi_city_size WHERE id IN ($this->citysize)";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":ids",$this->citysize);
                    $data = $command->query();
                    $result ='';
                    while ($citysize = $data->read())
                    {
                        $result .= $citysize['title'].",<br>";
                    }
                    return $result;
                }
                break;

            case 'region':
                if ($this->region != '')
                {
                    $sql = "SELECT title FROM tbl_omi_regions WHERE id IN ($this->region)";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":ids",$this->region);
                    $data = $command->query();
                    $result ='';
                    while ($region = $data->read())
                    {
                        $result .= $region['title'].",<br>";
                    }
                    return $result;
                }
                break;

            case 'city':
                if ($this->city != '')
                {
                    $sql = "SELECT title FROM tbl_omi_cities WHERE id IN ($this->city)";
                    $command = $connection->createCommand($sql);
                    $command->bindParam(":ids",$this->city);
                    $data = $command->query();
                    $result ='';
                    while ($city = $data->read())
                    {
                        $result .= $city['title'].",<br>";
                    }
                    return $result;
                }
                break;


        }
    }
    
    public static function getAudienceParams($quiz_id)
    {
        $model = self::model()->findAllByAttributes(array('quiz_id'=>$quiz_id));
        $modelQuiz = Quiz::model()->findByPk($quiz_id);
        $result = '';
        $i = 1;
        foreach($model as $item)
        {
            $result .= "<div style = 'border: 1px dashed #d0d0d0;margin: 10px;'>";
                $result .= "<p><b>Аудитория №".$i."</b></p>";
                $result .= "<p><b>Возраст:</b>".$item->age_from.'-'.$item->age_to."</p>";
                $result .= "<p><b>Пол:</b>".$item->getField('sex')."</p>";
                $result .= "<p><b>Образование:</b>".$item->getField('education')."</p>";
                $result .= "<p><b>Сфера деятельности:</b>".$item->getField('jobsphere')."</p>";
                $result .= "<p><b>Мат положение семьи:</b>".$item->getField('evaluation')."</p>";
                $result .= "<p><b>Размер города:</b>".$item->getField('citysize')."</p>";
                $result .= "<p><b>Регион:</b>".$item->getField('region')."</p>";
                $result .= "<p><b>Город:</b>".$item->getField('city')."</p>";
                $result .= "<p><b>Ограничение по кол-ву респондентов:</b>".$item->limit."</p>";
                $result .= "<p>Ссылка для данной аудитории http://panel.queryagent.ru/?h=".$modelQuiz->hash."&omi_aud_id=".$item->id."</p>";
            $result .= "</div>";
            ++$i;
        }
        return $result;
    }
}