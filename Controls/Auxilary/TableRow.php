<?php
/**
 * TableRow class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Auxiliary
 */
class TableRow extends Control
{
	public $Columns;
	public $Span;
	
	function TableRow()
	{
		parent::Control(0, 0, null, 20);
		$this->LayoutType = 1;
		$this->Columns = new ArrayList();
		$this->Columns->ParentId = $this->Id;
	}
	
	function Show()
	{
		$intialProperties = parent::Show();
		//$intialProperties .= ",'style.border','0px'";
		NolohInternal::Show("TR", $intialProperties, $this, $this->ParentId."InnerTBody");
	}
	
	function SearchEngineShow()
	{
		foreach($this->Columns as $column)
			$column->SearchEngineShow();
	}
}
?>