<?php
/**
 * Table class
 *
 * We're sorry, but this class doesn't have a description yet. We're working very hard on our documentation so check back soon!
 * 
 * @package Controls/Core
 */
class Table extends Control
{
	public $Rows;
	public $BuiltMatrix;
	private $ScrollLeft;
	private $ScrollTop;
	
	function Table($left=0, $top=0, $width=500, $height=500)
	{
		parent::Control($left, $top, $width, $height);
		$this->Rows = new ArrayList();
		$this->Rows->ParentId = $this->Id;
	}
	function BuildTable($numRows, $numCols, $typeAsString, $params='')
	{
		$this->BuiltMatrix = array(array());
		for($i = 0; $i < $numRows; ++$i)
		{
			$this->Rows->Add(new TableRow());
			for($j = 0; $j < $numCols; ++$j)
			{
				eval('$this->Rows->Elements[$i]->Columns->Add(new TableColumn(new '.$typeAsString.'(' . $params . ')));');
				$this->BuiltMatrix[$i][$j] = &$this->Rows->Elements[$i]->Columns->Elements[$j];
				// Added This Line To Make Default Control Width Equal to Column Width
				$this->BuiltMatrix[$i][$j]->Controls->Elements[0]->SetWidth($this->BuiltMatrix[$i][$j]->GetWidth());
			}
		}
	}
	function GetScrollLeft()
	{
		return $this->ScrollLeft;
	}
    function SetScrollLeft($scrollLeft)
    {
    	$scrollLeft = $scrollLeft==Layout::Left?0: $scrollLeft==Layout::Right?9999: $scrollLeft;
        if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollLeft\'', $scrollLeft), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollLeft', $scrollLeft, $this);
        $this->ScrollLeft = $scrollLeft;
    }
    function GetScrollTop()
    {
    	return $this->ScrollTop;
    }
    function SetScrollTop($scrollTop)
    {
    	$scrollTop = $scrollTop==Layout::Top?0: $scrollTop==Layout::Bottom?9999: $scrollTop;
    	if($_SESSION['_NIsIE'])
    		QueueClientFunction($this, 'NOLOHChange', array('\''.$this->Id.'\'', '\'scrollTop\'', $scrollTop), false, Priority::High);
    	else
        	NolohInternal::SetProperty('scrollTop', $scrollTop, $this);
        $this->ScrollTop = $scrollTop;
    }
	function Show()
	{
		$initialProperties = parent::Show();
		$id = $this->Id;
		$initialProperties .= ",'style.overflow','auto'";
		NolohInternal::Show('DIV', $initialProperties, $this);
		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
//		$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.borderCollapse','collapse','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		//$initialProperties = "'id','{$id}InnerTable','cellpadding','0','cellspacing','0','style.position','relative','style.width','{$this->Width}px','style.height','{$this->Height}px'";
		NolohInternal::Show('TABLE', $initialProperties, $this, $id);
		$initialProperties = "'id','{$id}InnerTBody', 'style.position','relative'";
		NolohInternal::Show('TBODY', $initialProperties, $this, $id.'InnerTable');
	}
	function SearchEngineShow()
	{
		foreach($this->Rows as $row)
			$row->SearchEngineShow();
	}
}
?>