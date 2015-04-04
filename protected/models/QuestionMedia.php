<?php

/**
 * This is the model class for table "{{question_media}}".
 *
 * The followings are the available columns in table '{{question_media}}':
 * @property integer $id
 * @property string $question_id
 * @property string $link
 */
class QuestionMedia extends CActiveRecord
{
    public $image;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return QuestionMedia the static model class
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
		return '{{question_media}}';
	}

        public function beforeSave()
        {
            if(parent::beforeSave())
            {
                // THIS is how you capture those uploaded images: remember that in your CMultiFile widget, you set 'name' => 'images'
                // proceed if the images have been set
                $name = Yii::getPathOfAlias('webroot').self::getPath().$this->link;
                if (isset($this->image)) {
                    $this->deleteImage();
                    // go through each uploaded image
                    //foreach ($images as $image => $pic) {

                        self::saveImage($this->image, $name);
                    //}
                }
                return true;
            }
            return false;
        }

        public function beforeDelete()
        {
            if(parent::beforeDelete())
            {
                $this->deleteImage(); // удалили модель? удаляем и файл
                return true;
            }
            return false;
        }

        public static function saveImage($image, $name)
        {
            if ($image->saveAs($name)) {
                // add it to the main model now
                $image = Yii::app()->image->load($name);
                list($width, $height, $type, $attr) = getimagesize($name);
                if($width > 500){
                    $image->resize(500, 500);
                    $image->save();
                }
            }
        }

        public function deleteImage()
        {
            $imagePath=Yii::getPathOfAlias('webroot').self::getPath().$this->link;
            if(is_file($imagePath))
                unlink($imagePath);
        }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
                        array('link', 'required'),
			array('question_id', 'length', 'max'=>50),
			array('link', 'length', 'max'=>150),
                        array('image', 'file', 'types'=>'jpg, gif, png','allowEmpty'=>true,'safe'=>false),
                        array('link','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, question_id, link', 'safe', 'on'=>'search'),
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
                    'question' => array(self::BELONGS_TO, 'Question', 'question_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'question_id' => 'Question',
			'link' => 'Link',
			'image' => 'Image',
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
		$criteria->compare('question_id',$this->question_id,true);
		$criteria->compare('link',$this->link,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

        public static function getPath(){
            //return Yii::getPathOfAlias('webroot').'/images/questions/';
            return '/upload/questions/';
        }
}