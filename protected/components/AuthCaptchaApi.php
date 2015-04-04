<?php

    class AuthCaptchaApi{
        private $path = '/upload/captcha/';

        private $ext = '.png';

        public $width = 240;
	/**
	 * @var integer the height of the generated CAPTCHA image. Defaults to 50.
	 */
	public $height = 100;
	/**
	 * @var integer padding around the text. Defaults to 2.
	 */
	public $padding = 12;
	/**
	 * @var integer the background color. For example, 0x55FF00.
	 * Defaults to 0xFFFFFF, meaning white color.
	 */
	public $backColor = 0xFFFFFF;
	/**
	 * @var integer the font color. For example, 0x55FF00. Defaults to 0x2040A0 (blue color).
	 */
	public $foreColor = 0x2040A0;
	/**
	 * @var boolean whether to use transparent background. Defaults to false.
	 */
	public $transparent = true;
	/**
	 * @var integer the minimum length for randomly generated word. Defaults to 6.
	 */
	public $minLength = 3;
	/**
	 * @var integer the maximum length for randomly generated word. Defaults to 7.
	 */
	public $maxLength = 3;
	/**
	 * @var integer the offset between characters. Defaults to -2. You can adjust this property
	 * in order to decrease or increase the readability of the captcha.
	 * @since 1.1.7
	 **/
	public $offset = -2;
	/**
	 * @var string the TrueType font file. Defaults to Duality.ttf which is provided
	 * with the Yii release.
	 */
	public $fontFile = '/Fonts/tahoma.ttf';

        /**
	 * Generates a new verification code.
	 * @return string the generated verification code
	 */
	public function generateVerifyCode()
	{
		if($this->minLength < 3)
			$this->minLength = 3;
		if($this->maxLength > 20)
			$this->maxLength = 20;
		if($this->minLength > $this->maxLength)
			$this->maxLength = $this->minLength;
		$length = mt_rand($this->minLength,$this->maxLength);

		$letters = 'bcdfghjklmnpqrstvwxyz';
		$vowels = 'aeiou';
		$numbers = '1234567890';
		$code = '';
		for($i = 0; $i < $length; ++$i)
		{
			if($i % 2 && mt_rand(0,10) > 2 || !($i % 2) && mt_rand(0,10) > 9)
				$code.=$vowels[mt_rand(0,4)];
			else
				$code.=$letters[mt_rand(0,20)];
                        if($i == $length / 2 || mt_rand(0,7) > $length / 2)
                                $code.=$numbers[mt_rand(0,9)];
		}

		return $code;
	}

        /**
	 * Renders the CAPTCHA image based on the code.
	 * @param string $code the verification code
	 * @return string image content
	 */
	public function renderImage($code)
	{
            $image = imagecreatetruecolor($this->width, $this->height);

            $backColor = imagecolorallocate($image,
                            (int)($this->backColor % 0x1000000 / 0x10000),
                            (int)($this->backColor % 0x10000 / 0x100),
                            $this->backColor % 0x100);
            imagefilledrectangle($image,0,0,$this->width,$this->height,$backColor);
            imagecolordeallocate($image,$backColor);

            if($this->transparent)
                    imagecolortransparent($image,$backColor);

            $foreColor = imagecolorallocate($image,
                            (int)($this->foreColor % 0x1000000 / 0x10000),
                            (int)($this->foreColor % 0x10000 / 0x100),
                            $this->foreColor % 0x100);

            $this->fontFile = Yii::getPathOfAlias('webroot') . $this->fontFile;

            $length = strlen($code);
            $box = imagettfbbox(30,0,$this->fontFile,$code);
            $w = $box[4] - $box[0] + $this->offset * ($length - 1);
            $h = $box[1] - $box[5];
            $scale = min(($this->width - $this->padding * 2) / $w,($this->height - $this->padding * 2) / $h);
            $x = 10;
            $y = round($this->height * 27 / 40);
            for($i = 0; $i < $length; ++$i)
            {
                    $fontSize = (int)(rand(32,40) * $scale * 0.8);
                    $angle = rand(-10,10);
                    $letter = $code[$i];
                    $box = imagettftext($image,$fontSize,$angle,$x,$y,$foreColor,$this->fontFile,$letter);
                    $x = $box[2] + $this->offset;
            }

            imagecolordeallocate($image,$foreColor);

            $index_img = $this->generateVerifyCode();
            $filename = Yii::getPathOfAlias('webroot') . $this->path . $index_img . $this->ext;

            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Transfer-Encoding: binary');
            header("Content-type: image/png");
            imagepng($image, $filename);
            imagedestroy($image);
            return $this->path . $index_img . $this->ext;
	}

        public static function getCaptcha(){
            $self = new AuthCaptchaApi();
            $code = $self->generateVerifyCode();
            $self->renderImage($code);
            return $code;
        }
    }

?>
