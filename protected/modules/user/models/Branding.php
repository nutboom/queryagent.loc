<?php

/**
 * This is the model class for table "{{licenses}}".
 *
 * The followings are the available columns in table '{{licenses}}':
 * @property string $id
 * @property string $user
 * @property string $active
 * @property integer $tariff
 * @property string $date_expirate
 * @property string $date_open
 */
class Branding extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Licenses the static model class
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
		return '{{branding}}';
	}

	protected function beforeValidate() {
		if (parent::beforeValidate()) {
			if ($_FILES['logo']['error'] == 0) {
				$logo	=	CUploadedFile::getInstanceByName('logo');
				$ext	=	$logo->getExtensionName();
				$name	=	dechex(rand()%999999999) . '.' . $ext;
				$path	=	Yii::getPathOfAlias('webroot').'/upload/branding/'.$name;

				if ($logo->saveAs($path)) {
					$image = Yii::app()->image->load($path);
					list($width, $height, $type, $attr) = getimagesize($path);
					if ($width != 100 || $height != 40) {
						$this->addError('logo', Yii::t('app', 'Allow size of logotype is 100px on 40px'));
					}
					else {
						if ($this->logo) {
							@unlink(Yii::getPathOfAlias('webroot').'/upload/branding/'.$this->logo);
						}

						$this->logo = $name;
					}
				}
			}

            if ($_FILES['logo_social']['error'] == 0) {
                $logo	=	CUploadedFile::getInstanceByName('logo_social');
                $ext	=	$logo->getExtensionName();
                $name	=	dechex(rand()%999999999) . '.' . $ext;
                $path	=	Yii::getPathOfAlias('webroot').'/upload/branding/'.$name;

                if ($logo->saveAs($path)) {
                    $image = Yii::app()->image->load($path);
                    list($width, $height, $type, $attr) = getimagesize($path);
                    if ($width != 200 || $height != 200) {
                        $this->addError('logo', "Размер логотипа 200 X 200 px");
                    }
                    else {
                        if ($this->logo_social) {
                            @unlink(Yii::getPathOfAlias('webroot').'/upload/branding/'.$this->logo_social);
                        }

                        $this->logo_social = $name;
                    }
                }
            }

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
			array('user, top_color, left_color', 'required'),
			array('user', 'numerical', 'integerOnly'=>true),
			array('logo', 'safe'),
			array('user, top_color, left_color, logo', 'safe', 'on'=>'search'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'user' => 'user',
			'top_color' => Yii::t('app', 'Top color branding #'),
			'left_color' => Yii::t('app', 'Left color branding #'),
			'logo' => 'logo',
		);
	}

	public static function getTopColor($user = null) {
		$default = "#046183";

		if (!$user) {
			if (!Yii::app()->user->isGuest) {
				$user = Yii::app()->user->id;
			}
			else {
				return $default;
			}
		}

		$branding = User::model()->findByPk($user)->branding;

		if (!$branding) {
			return $default;
		}

		return ($branding->top_color) ? $branding->top_color : $default;
	}

	public static function getLeftColor($user = null) {
		$default = "#44a5c5";

		if (!$user) {
			if (!Yii::app()->user->isGuest) {
				$user = Yii::app()->user->id;
			}
			else {
				return $default;
			}
		}

		$branding = User::model()->findByPk($user)->branding;

		if (!$branding) {
			return $default;
		}

		return ($branding->left_color) ? $branding->left_color : $default;
	}

	public static function getLogo($user = null) {
		$default = "/images/logo.png";

		if (!$user) {
			if (!Yii::app()->user->isGuest) {
				$user = Yii::app()->user->id;
			}
			else {
				return $default;
			}
		}

		$branding = User::model()->findByPk($user)->branding;

		if (!$branding) {
			return $default;
		}

		return ($branding->logo) ? "/upload/branding/" . $branding->logo : $default;
	}


	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('user',$this->user,true);
		$criteria->compare('top_color',$this->top_color,true);
		$criteria->compare('left_color',$this->left_color,true);
		$criteria->compare('logo',$this->logo,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}