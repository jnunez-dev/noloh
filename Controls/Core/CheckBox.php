<?php
/**
 * CheckBox class
 *
 * A Control for a conventional web checkbox. Checkboxes are usually used to indicate
 * whether a condition is on/off, yes/no, or true/false. Checkbox is simlar to RadioButton,
 * except that RadioButton's are usually limited to one choice, while a CheckBox allows for
 * multiple choices.
 * 
 * The following is an example of instantiating and adding a CheckBox
 * <code>
 * function Foo()
 * {
 *     $tmpCheck = new CheckBox("CheckBox1", 0,0);
 *     //Adds a CheckBox to the Controls class of some Container object
 *     $this->Controls->Add($tmpCheck);
 * }
 * </code>
 * 
 * @package Controls/Core
 */
class CheckBox extends CheckControl implements MultiGroupable
{
    /**
     * Constructor.
	 * Be sure to call this from the constructor of any class that extends CheckBox
	 *	<code> $tmpCheck = new CheckBox("Check Me", 0, 0, 100, 24);</code>
     * @param string $text The text of the CheckBox
     * @param integer $left The left coordinate of this element.
     * @param integer $top The top coordinate of this element.
     * @param mixed $width The Width of this element, possible values are
     * integer, percentage, System::Auto
     * @param mixed $height The Height of this element, possible values are
     * integer, percentage, System::Auto
     * @return CheckBox
     */
	function CheckBox($text='', $left = 0, $top = 0, $width = 50, $height = 20)
	{
		parent::CheckControl($text, $left, $top, $width, $height);
	}
	/**
	 * @ignore
	 */
	function GetEventString($eventTypeAsString)
	{
		if($eventTypeAsString == 'Click' || $eventTypeAsString == 'Change')
           return '_NCBSave("'.$this->Id.'");' . parent::GetEventString($eventTypeAsString);
		return parent::GetEventString($eventTypeAsString);
	}
	/**
	 * @ignore
	 */
	function Show()
	{
        parent::Show();
		$initialProperties = '\'id\',\''.$this->Id.'I\',\'type\',\'checkbox\',\'defaultChecked\','.($this->Checked?'true':'false').parent::GetEventString(null);
        if($this->GroupName != null)
            $initialProperties .= ',\'name\',\''.$this->GroupName.'\'';
		NolohInternal::Show('INPUT', $initialProperties, $this, $this->Id);

        /*
		$initialProperties = parent::Show();
		$initialProperties .= ",'type','checkbox'";
		$initialProperties .= parent::GetEventString(null);
		NolohInternal::Show('INPUT', $initialProperties, $this);    */
		//return $initialProperties;
	}
}	
?>