<?php
if(!$_SERVER['HTTP_REFERER'] || stripos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) === false) {
	die('HACK?');
}

define('MODX_API_MODE', true);
include_once(dirname(__FILE__) . "../../../index.php");

$c = new Captcha;
$c->run();

class Captcha {

	public function run() {
		$captcha = $this->generate_code();
		$this->img_code($captcha);
	}

	/**
	 * @return string
	 */
	private function generate_code() {
		//$chars = '1234567890абвгдеёжзийклмнопрстуфхцчшщъыэюя';
		$chars = '1234567890';
		$length = 5;
		$numChars = mb_strlen($chars, 'UTF-8');
		$str = '';
		for($i = 0; $i < $length; $i++) {
			$str .= mb_substr($chars, rand(1, $numChars) - 1, 1, 'UTF-8');
		}
		$array_mix = preg_split('//ui', $str, -1, PREG_SPLIT_NO_EMPTY);
		srand((float) microtime() * 1000000);
		shuffle($array_mix);
		return implode("", $array_mix);
	}

	/**
	 * @param $code
	 */
	private function img_code($code) {
		header("Pragma: no-cache");
		header("Content-Type:image/png");
		if($_REQUEST['key']) {
			$_SESSION['veriword_' . md5($_REQUEST['key'])] = $code;
		} else {
			$_SESSION['veriword'] = $code;
		}
		$im = imagecreate(210, 100);
		imagecolorallocatealpha($im, 255, 255, 255, 127);
		$color = imagecolorallocate($im, 0, 0, 0);
		$x = 10;
		for($i = 0; $i < strlen($code); $i++) {
			$letter = mb_substr($code, $i, 1, 'UTF-8');
			imagettftext($im, 70, rand(-10, 10), $x, 75, $color, "./font.ttf", $letter);
			$x += 35;
		}
		imagepng($im);
		imagedestroy($im);
	}

}