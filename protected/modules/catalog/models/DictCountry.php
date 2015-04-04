<?php

/**
 * This is the model class for table "{{dict_country}}".
 *
 * The followings are the available columns in table '{{dict_country}}':
 * @property integer $dict_country_id
 * @property string $title
 *
 * The followings are the available model relations:
 * @property DictCity[] $dictCities
 */
class DictCountry extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DictCountry the static model class
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
		return '{{dict_country}}';
	}

	protected function beforeSave() {
		if(parent::beforeSave()) {
			$this->last_update = new CDbExpression('NOW()');

			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('title', 'required'),
			array('title', 'length', 'max'=>150),
                        array('title','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('dict_country_id, title', 'safe', 'on'=>'search'),
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
			'cities' => array(self::HAS_MANY, 'DictCity', 'country_id'),
                        'audience'  =>  array(self::MANY_MANY, 'TargetAudience', 'tbl_link_target_audience_country(target_audience_id, country_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'dict_country_id' => 'Dict Country',
			'title' => Yii::t('app', 'Title'),
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

		$criteria->compare('dict_country_id',$this->dict_country_id);
		$criteria->compare('title',$this->title,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public static function scopeWithCities(){
            $data = array();
            $countriesData = DictCountry::model()->with('cities')->with('audience')->findAll();
            foreach($countriesData as $i=>$country){
                /*echo '<pre>';
                print_r($country->audience);
                echo '</pre>';*/
                $element = array();
                $element['text'] = CHtml::label(CHtml::checkBox('TargetAudience[country_audience[]',false,array('value'=>$country->dict_country_id, 'id'=>'Country_audience_'.$i)).CHtml::label($country->title, 'Country_audience_'.$i),'',array('class'=>'checkbox'));
                $element['children'] = array();
                foreach($country->cities as $j=>$city){
                    array_push($element['children'], array('text'=>CHtml::label(CHtml::checkBox('TargetAudience[city_audience][]',false,array('value'=>$city->dict_city_id, 'id'=>'City_audience_'.$i.'_'.$j)).CHtml::label($city->title, 'City_audience_'.$i.'_'.$j),'',array('class'=>'checkbox'))));
                }
                array_push($data, $element);
            }
            return $data;
        }
}