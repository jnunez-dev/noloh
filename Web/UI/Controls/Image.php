<?php
/**
* @package Web.UI.Controls
* Image class file.
*/

/**
 * Image class
 *
 * A Control for an Image. An image can either be used to diplay a graphic, or be used as a custom button.
 *
 * 
 * Example 1: Instantiating and Adding an Image
 *
 * <code>
 *function Foo()
 *{
 *    //Instatiates $tmpImage as a new Image, with the src of SomePicture.gif, and a left, 
 *    //and top of 10px.
 *    $tmpImage = new Image("Images/SomePicture.gif", 10, 10);
 *    $this->Controls->Add($tmpImage); //Adds a button to the Controls of some Container
 *}     	
 *</code>
 * 
 * @property string $Src The source file of this image
 * 
 */
class Image extends Control 
{
	/**
	* Src, The source file of the image.
	* @var string
	*/
	private $Src;
    private $Magician;
	
	/**
	* Constructor.
	* for inherited components, be sure to call the parent constructor first
 	* so that the component properties and events are defined.
 	* Example
 	*	<code> $tempVar = new Image("Images/NOLOHLogo.gif", 0, 10);</code>
 	* @param string[optional]
	* @param integer[optiona]
	* @param integer[optional]
	* @param integer[optional] //The Width of the Image is determined automatically if not explicitly set
	* @param integer[optional] //The Height of the Image is determined automatically if not explicitly set
	*/
	function Image($src='', $left = 0, $top = 0, $width = System::Auto, $height = System::Auto)  
	{
		parent::Control($left, $top, null, null);
		if(!empty($src))
			$this->SetSrc($src);
		$this->SetWidth($width);
		$this->SetHeight($height);
	}
	/**
	* Gets the Src of the Image
	* <b>Note:</b>Can also be called as a property.
	*<code> $tempSrc = $this->Src;</code>
	* @return string|absolute path
 	*/
	function GetSrc()
	{
		return $this->Src;
	}
	/**
	*Sets the Src of the Image.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Src = "Images/NewImage.gif";</code>
	*The path is relative to your main file 
	*<b>!Important!</b> If Overriding, make sure to call parent::SetSrc($newSrc)
	*@param string $Src
	*@return string|Src
	*/
	function SetSrc($newSrc, $adjustSize=false)
	{
		$this->Src = $newSrc;
		if($this->Magician == null)
			NolohInternal::SetProperty('src', $newSrc, $this);
		else
			$this->SetMagicianSrc();
        //NolohInternal::SetProperty('src', $this->Magician == null ? $newSrc : ($_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$this->Magician[0].'&Function='.$this->Magician[1].'&Params='.implode(',', array_slice($this->Magician, 2))), $this);
		if($adjustSize)
		{
			$this->SetWidth(System::Auto);
			$this->SetHeight(System::Auto);
		}
		return $newSrc;
	}
	/**
	*Gets the Width of the Image.
	*<b>Note:</b>Can also get as a property.
	*<code>$tmpVar = $this->Width;</code>
	*@param string $unit[optional] //Units you would like the width in, either px, or "%".
	*@return mixed
	*/
	function GetWidth($unit='px')
	{
		if($unit == '%')
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetWidth()/$tmpImageSize[0] * 100;
		}
		else
			return parent::GetWidth();
	}
	/**
	*Sets the Width of the Image.
	*<b>Note:</b>Can also be set as a property.
	*<code>$this->Width = 200;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetWidth($newWidth)
	*@param integer $Width
	*/
	function SetWidth($width)
	{
		$tmpWidth = $width;
		if(!is_numeric($tmpWidth))
		{
			if(substr($width, -1) != '%')
			{
				$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
				if($tmpWidth == System::Auto)
					$tmpWidth = $tmpImageSize[0];
				else
				{
					$tmpWidth = intval($tmpWidth)/100;
					$tmpWidth = round($tmpWidth * $tmpImageSize[0]);
				}
			}
		}
		if($this->Magician != null)
			$this->SetMagicianSrc();
		parent::SetWidth($tmpWidth);
	}
	/**
	*Gets the Width of the Image.
	*<b>Note:</b>Can also get as a property.
	*<code>$tmpVar = $this->Height;</code>
	*@param string $unit[optional|] //Units you would like the height in, either px, or "%".
	*@return mixed
	*/
	function GetHeight($unit='px')
	{
		if($unit == '%')
		{
			$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
			return parent::GetHeight()/$tmpImageSize[1] * 100;
		}
		else
			return parent::GetHeight();
	}
	/**
	*Sets the Height of the Image.
	*<b>Note:</b>Can also be set as a property. 
	*<code>$this->Height = 200;</code>
	*<b>!Important!</b> If Overriding, make sure to call parent::SetHeight($newHeight)
	*@param integer $height
	*/
	function SetHeight($height)
	{
		$tmpHeight = $height;
		if(!is_numeric($tmpHeight))
		{
			if(substr($height, -1) != '%')
			{
				$tmpImageSize = getimagesize(GetAbsolutePath($this->Src));
				if($tmpHeight == System::Auto)
					$tmpHeight = $tmpImageSize[1];
				else
				{
					$tmpHeight = intval($tmpHeight)/100;
					$tmpHeight = round($tmpHeight * $tmpImageSize[1]);
				}
			}
		}
		if($this->Magician != null)
			$this->SetMagicianSrc();
		parent::SetHeight($tmpHeight);
	}

    function Conjure($className, $functionName, $paramsAsDotDotDot = null)
    {
		$this->Magician = func_get_args();
		$this->SetMagicianSrc();
        //NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$className.'&Function='.$functionName.'&Params='.implode(',', array_slice($this->Magician, 2)), $this);
        //$this->Magician = array($className, $functionName);
    }
	/**
	* @ignore
	*/
	private function SetMagicianSrc()
	{
		if($this->Src)
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$this->Magician[0].'&Function='.$this->Magician[1].'&Params='.urlencode(implode(',', array_slice($this->Magician, 2))), $this);
		else
			NolohInternal::SetProperty('src', $_SERVER['PHP_SELF'].'?NOLOHImage='.GetAbsolutePath($this->Src).'&Class='.$this->Magician[0].'&Function='.$this->Magician[1].'&Params='.urlencode(implode(',', array_slice($this->Magician, 2))).'&Width='.$this->GetWidth().'&Height='.$this->GetHeight(), $this);
	}
	/**
	* @ignore
	*/
	function Show()
	{
		NolohInternal::Show('IMG', parent::Show(), $this);
	}
	
	function SearchEngineShow()
	{
		print('<IMG src="'.$this->Src.'"' . ($this->ToolTip==null?'':(' alt="'.$this->ToolTip.'"')) . '></IMG> ');
	}
	
	/**
	 *@ignore 
	*/
	static function MagicGeneration($src, $class, $function, $params, $width=300, $height=200)
	{
		if($src != '')
		{
			$splitString = explode('.', $src);
			$extension = strtolower($splitString[count($splitString)-1]);
			if($extension == 'jpg')
				$extension = 'jpeg';
			elseif($extension == 'bmp')
				$extension = 'wbmp';
			//eval('if(imagetypes() & IMG_'.strtoupper($extension).')' .
			//	'$im = imagecreatefrom'.$extension.'($src);');
			if(imagetypes() & constant('IMG_'.strtoupper($extension)))
				$im = call_user_func('imagecreatefrom'.$extension, $src);
		}
		else
		{
			$extension = 'png';
			$im = imagecreatetruecolor($width, $height);
			$white = imagecolorallocate($im, 255, 255, 255);
			imagefill($im, 0, 0, $white);
		}
		if($im)
		{
			call_user_func_array(array($class, $function), array_merge(array($im), explode(',', urldecode($params))));
			header('Content-type: image/'.$extension);
			call_user_func('image'.$extension, $im);
			imagedestroy($im);
		}
	}
}
?>