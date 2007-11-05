<?php
/**
 * @package Web.UI.Controls
 */
class MarkupRegion extends Control
{
	private $CachedWidth;
	private $CachedHeight;
	//private $FontSize;
	
	function MarkupRegion($markupStringOrFile, $left=0, $top=0, $width = 200, $height = 200)
	{
		parent::Control($left, $top, $width, $height);
		$this->Scrolling = System::Auto;
		//$this->AutoScroll = true;
		$this->SetText($markupStringOrFile);
	}
	function GetFontSize()
	{
		return 12;
	}
//	function SetFontSize($newSize)
//	{
//		$this->FontSize = $newSize;
//		$this->AutoWidthHeight();
//		NolohInternal::SetProperty("style.fontSize",$this->FontSize."px",$this);
//	}
	function GetWidth()
	{
		$Width = parent::GetWidth();
		return ($Width == System::Auto || $Width == System::AutoHtmlTrim) ? $this->CachedWidth : $Width;
	}
	function GetHeight()
	{
		$Height = parent::GetHeight();
		return ($Height == System::Auto || $Height == System::AutoHtmlTrim)? $this->CachedHeight : $Height;
	}
	//function GetMarkupString()
	//{
	//	return $this->MarkupString;
	//}
	//TODO, DON'T LIKE THAT THIS IS REPEATED FROM LABEL, BUT, MarkupRegion IS A PANEL, AND NOT A LABEL, SOMETHING TO THINK ABOUT - Asher
	protected function AutoWidthHeight($markup)
	{
		$width = parent::GetWidth();
		$height = parent::GetHeight();
		//Added Strip Tags
		
		if($width == System::Auto || $height == System::Auto)
			$widthHeight = AutoWidthHeight($markup, $width, $height, $this->GetFontSize());
		elseif($width == System::AutoHtmlTrim || $height == System::AutoHtmlTrim)
			$widthHeight = AutoWidthHeight(strip_tags(html_entity_decode($markup)), $width, $height, $this->GetFontSize());
		else
			return;
		if($width == System::Auto || $width == System::AutoHtmlTrim)
		{
			$this->CachedWidth = $widthHeight[0];
			NolohInternal::SetProperty('style.width', $this->CachedWidth.'px', $this);
		}
		if($height == System::Auto || $height == System::AutoHtmlTrim)
		{
			$this->CachedHeight = $widthHeight[1];
			NolohInternal::SetProperty('style.height', $this->CachedHeight.'px', $this);
		}
	}

    function SetText($markupStringOrFile)
	{

		//$this->IsFile = (is_file($whatMarkupStringOrFile))? true : false;
        parent::SetText($markupStringOrFile);
//		if(is_file($whatMarkupStringOrFile))
//		{
			$markupStringOrFile =  str_replace(array("\r\n", "\n", "\r", "\"", "'"), array('<Nendl>', '<Nendl>', '<Nendl>', '<NQt2>', '<NQt1>'), ($tmpFullString = ((is_file($markupStringOrFile))?file_get_contents($markupStringOrFile):$markupStringOrFile)));
			$this->AutoWidthHeight($tmpFullString);
			QueueClientFunction($this, 'SetMarkupString', array("'$this->Id'", "'$markupStringOrFile'"));
//		}
//		else
//			NolohInternal::SetProperty("innerHTML", $whatMarkupStringOrFile, $this);
	}
		//$this->MarkupString = file_get_contents($whatMarkupStringOrFile);
		//if($this->MarkupString == false)
			//$this->MarkupString = $whatMarkupStringOrFile;
//	}
	function Show()
	{
		parent::Show();
        NolohInternal::Show('DIV', parent::Show(), $this);
		//AddScriptSrc(NOLOHConfig::GetBaseDirectory().NOLOHConfig::GetNOLOHPath()."Javascripts/MarkupRegionScript.js");
		AddNolohScriptSrc('MarkupRegion.js');
	}
	function SearchEngineShow()
	{
		print(((is_file($this->Text))?file_get_contents($this->Text):$this->Text).' ');
	}
}

?>