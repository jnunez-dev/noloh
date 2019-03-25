<?php
/**
 * Raw Parameter
 *
 * The Raw Parameter class is used to inject code into SQL statements that don't need to be parsed
 * such as SQL function calls or object names like tables, columns, databases, etc
 */
class RawParameter extends Base
{
	protected $Parameter;
	
	function RawParameter($param)
	{
		/* Only allow certain characters used in SQL */
		if (preg_match("/^[a-zA-Z0-9_\(\)\:\s\*,\"]+$/", $param))
		{
			$this->Parameter = $param;
		}
		else
		{
			BloodyMurder('RawParameter parameter contains invalid characters');
		}
	}
	function GetParameter()
	{
		return $this->Parameter;
	}
}