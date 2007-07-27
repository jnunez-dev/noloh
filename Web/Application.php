<?php
/**
 * @package Web
 */
global $OmniscientBeing;

// DEPRECATED! Use Application::SetStartUpPage instead.
function SetStartUpPage($className, $unsupportedURL="", $URLTokenMode=URL::Display)
{
	new Application($className, $unsupportedURL, $URLTokenMode);
}

/**
* @ignore
*/
final class Application
{
	private $WebPage;
	
	public static function SetStartUpPage($className, $unsupportedURL="", $URLTokenMode=URL::Display)
	{
		new Application($className, $unsupportedURL, $URLTokenMode);
	}
	
	/**
	* Resets Application to original state
	*/
	public static function Reset()
	{
		session_destroy();
		session_unset();
		print(
"/*~NScript~*/
frm = document.createElement('FORM');
frm.action = window.location;
frm.method = 'post';
document.body.appendChild(frm);
frm.submit();"
		);
		die();
	}
	
	public function Application($className, $unsupportedURL, $URLTokenMode)
	{
		session_name(hash('md5', $_SERVER['PHP_SELF']));
		//ini_set('session.gc_probability', 50);
		session_start();
		//header("Content-type: text/javascript");
		//ini_set('zlib_output_compression','On'); 
		
		if(isset($_GET['NOLOHImage']))
			Image::MagicGeneration($_GET['NOLOHImage'], $_GET['Class'], $_GET['Function']);
		elseif(isset($_GET['NOLOHFileUpload']))
		{
			FileUpload::ShowInside($_GET['NOLOHFileUpload'], $_GET['Width'], $_GET['Height']);
			if(isset($_FILES['NOLOHFile']) && $_FILES['NOLOHFile']['tmp_name']!="")
			{
				rename($_FILES['NOLOHFile']['tmp_name'], $_FILES['NOLOHFile']['tmp_name']."N");
				$_SESSION['NOLOHFiles'][$_GET['NOLOHFileUpload']] = $_FILES['NOLOHFile'];
				$_SESSION['NOLOHFiles'][$_GET['NOLOHFileUpload']]['tmp_name'] .= "N";
			}
		}
		elseif(isset($_SESSION['NOLOHVisit']) || isset($_POST['NOLOHVisit']))
		{
			if(!isset($_SESSION['NOLOHVisit']) || /*$_SESSION['NOLOHURL'] != $_SERVER['PHP_SELF'] || */
			  (((!isset($_POST['NOLOHVisit']) && !isset($_POST['NOLOHServerEvent'])) || !isset($_SERVER['HTTP_REMOTE_SCRIPTING'])) && $_SESSION['NOLOHVisit']>=0) ||
			  (isset($_POST['NOLOHVisit']) && $_SESSION['NOLOHVisit'] != $_POST['NOLOHVisit']))
			{
				//if(isset($_POST['NOLOHVisit']) && $_SESSION['NOLOHVisit'] != $_POST['NOLOHVisit'])
					//print("alert('" . $_SESSION['NOLOHVisit'] . " vs " . $_POST['NOLOHVisit'] . "');");
				//	print("location.reload(true);");
				if(isset($_SERVER['HTTP_REMOTE_SCRIPTING']) || isset($_POST['NOLOHServerEvent']) || !isset($_SESSION['NOLOHVisit']) || isset($_GET["NWidth"]))
					self::Reset();
				session_destroy();
				session_unset(); 
				self::SetStartUpPage($className, $unsupportedURL, $URLTokenMode);
				return;
			}
			if(isset($_POST['NoSkeleton']) && GetBrowser()=="ie")
			{
				$srcs = $_SESSION['NOLOHScriptSrcs'];
				$_SESSION = array();
				$this->HandleFirstRun($className, $unsupportedURL, false);
				$_SESSION['NOLOHScriptSrcs'] = $srcs;
				//$_SESSION['NOLOHVisit'] = -1;
				AddScript("NOLOHVisit=-1", Priority::High);
			}
			$GLOBALS["NOLOHURLTokenMode"] = $URLTokenMode;
			if(isset($_SESSION["NOLOHOmniscientBeing"]))
				$this->TheComingOfTheOmniscientBeing();
			if(!empty($_POST['NOLOHClientChanges']))
				$this->HandleClientChanges();
			if(!empty($_POST['NOLOHFileUploadId']))
				GetComponentById($_POST['NOLOHFileUploadId'])->File = &$_FILES['NOLOHFileUpload'];
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
				GetComponentById($key)->File = new File($val);
			if(!empty($_POST['NOLOHServerEvent']))
				$this->HandleServerEvent();
			foreach($_SESSION['NOLOHFiles'] as $key => $val)
			{
				unlink($_SESSION['NOLOHFiles'][$key]['tmp_name']);
				GetComponentById($key)->File = null;
				unset($_SESSION['NOLOHFiles'][$key]);
			}
			$this->Run();
		}
		else
			$this->HandleFirstRun($className, $unsupportedURL);
	}
	
	private function HandleFirstRun($className, $unsupportedURL, $trulyFirst=true)
	{
		$_SESSION['NOLOHVisit'] = -1;
		$_SESSION['NOLOHNumberOfComponents'] = 0;
		$_SESSION['NOLOHControlQueue'] = array();
		$_SESSION['NOLOHFunctionQueue'] = array();
		$_SESSION['NOLOHPropertyQueue'] = array();
		$_SESSION['NOLOHScript'] = array("", "", "");
		$_SESSION['NOLOHScriptSrcs'] = array();
		$_SESSION['NOLOHFiles'] = array();
		$_SESSION['NOLOHGarbage'] = array();
		$_SESSION['NOLOHStartUpPageClass'] = $className;
		$_SESSION['NOLOHURL'] = $_SERVER['PHP_SELF'];
		DeclareGlobal("HighestZIndex", 0);
		DeclareGlobal("LowestZIndex", 0);
		UserAgentDetect::LoadInformation();
		if($trulyFirst)
		{
			//$_SESSION['NOLOHVisit'] = -1;
			if($_SESSION["NOLOHBrowser"] == "other" && $_SESSION["NOLOHOS"] == "other")
			{
				//Search engine code here
			}
			else 
				WebPage::SkeletalShow($unsupportedURL);
		}
	}
	
	private function TheComingOfTheOmniscientBeing()
	{
		global $OmniscientBeing;
		$OmniscientBeing = unserialize(defined('FORCE_GZIP') ? gzuncompress($_SESSION['NOLOHOmniscientBeing']) : $_SESSION['NOLOHOmniscientBeing']);
		unset($_SESSION['NOLOHOmniscientBeing']);
		foreach($_SESSION['NOLOHGarbage'] as $id => $nothing)
		{
			$control = &$GLOBALS['OmniscientBeing'][$id];
			if(!isset($_SESSION['NOLOHGarbage'][$control->GetParentId()]) && $control->GetShowStatus()!==0 && $control instanceof Control)
				AddScript("_NAsc('$id')");
			unset($GLOBALS['OmniscientBeing'][$id]);
		}
		$_SESSION['NOLOHGarbage'] = array();
		//foreach($OmniscientBeing as $key => $val)
		//	$val->RestoreValues();
		$this->WebPage = GetComponentById($_SESSION['NOLOHStartUpPageId']);
	}
	
	private function HandleClientChanges()
	{
		$GLOBALS["PropertyQueueDisabled"] = true;
		//$runThisString = "";
		$splitChanges = explode("~d0~", $_POST['NOLOHClientChanges']);
		$numChanges = count($splitChanges);
		for($i = 0; $i < $numChanges; $i++)
		{
			//$runThisString = 'GetComponentById($splitChange[0])->';
			$splitChange = explode("~d1~", $splitChanges[$i]);
			switch($splitChange[1])
			{
				// Strings
				/*case "ViewMonth":
				case "ViewYear":
				case "Date":
				case "Month":
				case "Year":
				case "Text":
				case "Src":
				case "BackColor":
				case "Color":
				case "ZIndex":
				case "SelectedTab":
					GetComponentById($splitChange[0])->{$splitChange[1]} = $splitChange[2];
					//$runThisString .= $splitChange[1] . ' = "' . $splitChange[2] . '";';
					break;*/
				// Functions
				case "KillLater":
					if(GetComponentById($splitChange[0]) != null)
						GetComponentById($splitChange[0])->Close();
						/*$runThisString .= 'Close();';
					else
						$runThisString = "";
					break;*/
				//case "SelectedTab":
				//	$runThisString .= 'SelectedIndex = GetComponentById($splitChange[0])->TabControlBar->Controls->IndexOf(GetComponentById($splitChange[2]));';
					//break;
				// Booleans
				//case "Checked":
				//case "ClientVisible":
					//$runThisString .= $splitChange[1] . ' = ' . $splitChange[2] . ';';
					//break;
				// Explode string to array
				case "Items":
					GetComponentById($splitChange[0])->{$splitChange[1]} = self::ExplodeItems($splitChange[2]);
					break;
				case "SelectedIndices":					
					GetComponentById($splitChange[0])->{$splitChange[1]} = self::ExplodeSelectedIndices($splitChange[2]);
					break;
					//$tmp = strpos($splitChange[1], "->");
					//$runThisString = 'GetComponentById($splitChange[0])->';
					//$runThisString .= $splitChange[1] . ' = $this->Explode' . ($tmp===false?$splitChange[1]:substr($splitChange[1], 0, $tmp)) . '("' . $splitChange[2] . '");';
					//break;
				default:
					//$runThisString .= $splitChange[1] . ' = ' . $splitChange[2] . ';';
					GetComponentById($splitChange[0])->{$splitChange[1]} = $splitChange[2];
			}
			//echo $runThisString;
			//eval($runThisString);
		}
		unset($GLOBALS["PropertyQueueDisabled"]);
	}
	
	private function HandleServerEvent()
	{
		if(isset($_POST['NOLOHKey']))
			Event::$Key = $_POST['NOLOHKey'];
		if(isset($_POST['NOLOHCaught']))
			Event::$Caught = $this->ExplodeDragCatch($_POST['NOLOHCaught']);
		Event::$MouseX = $_POST["NOLOHMouseX"];
		Event::$MouseY = $_POST["NOLOHMouseY"];
		$splitEvent = explode("@", $_POST['NOLOHServerEvent']);
		$obj = GetComponentById($splitEvent[1]);
		if($obj != null)
			return $obj->{$splitEvent[0]}->Exec($execClientEvents=false);
		else 
		{
			$splitStr = explode("i", $splitEvent[1], 2);
			return GetComponentById($splitStr[0])->ExecEvent($splitEvent[0], $splitEvent[1]);
		}
		//$runThisString = 'return GetComponentById($splitEvent[1])->' . $splitEvent[0] . '->Exec(false);';
		//eval($runThisString);
	}

	private function Run()
	{
		global $OmniscientBeing;
		/*ini_set("zlib.output_compression", "On");
		ini_set("zlib.output_compression_level", 1);
		header("Content-Encoding: gzip");
		header("Vary: Accept-Encoding");*/
		if(defined('FORCE_GZIP'))
			ob_start("ob_gzhandler");
		/*global $HTTP_ACCEPT_ENCODING;
		if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
			header("Content-Encoding: x-gzip");
		elseif (strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false)
			header("Content-Encoding: gzip");
		else 
			Alert("neeeeh");*/
		//if(defined('FORCE_GZIP'))
			
		if(++$_SESSION['NOLOHVisit']==0)
		{
			if($GLOBALS["NOLOHURLTokenMode"] == 1)
			{
				$_SESSION['NOLOHTokens'] = $_GET;
				unset($_SESSION['NOLOHTokens']["NWidth"], $_SESSION['NOLOHTokens']["NHeight"]);
			}
			elseif($GLOBALS["NOLOHURLTokenMode"] == 2)
			{
				$split = explode("&", base64_decode(key($_GET)));
				$count = count($split);
				for($i=0; $i<$count; $i++)
				{
					$split2 = explode("=", $split[$i]."=");
					$_SESSION['NOLOHTokens'][$split2[0]] = $split2[1];
				}
			}
			$className = $_SESSION['NOLOHStartUpPageClass'];
			$this->WebPage = new $className();
			$_SESSION['NOLOHStartUpPageId'] = $this->WebPage->Id;
			$this->WebPage->Show();
		}
		if(isset($GLOBALS["NOLOHTokenUpdate"]) && (!isset($_POST['NoSkeleton']) || GetBrowser()!="ie"))
			URL::UpdateTokens();
		NolohInternal::ShowQueue();
		NolohInternal::FunctionQueue();
		NolohInternal::SetPropertyQueue();
		$sendStr = "/*~NScript~*/" . $_SESSION['NOLOHScript'][0] . $_SESSION['NOLOHScript'][1] . $_SESSION['NOLOHScript'][2];
		//if(strstr($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator')!==false || strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')===false)
		//	print($sendStr);
		//elseif(defined('FORCE_GZIP'))
		//	print(gzencode($sendStr,1));
		print($sendStr);
		$_SESSION['NOLOHScript'] = array("", "", "");
		$_SESSION['NOLOHOmniscientBeing'] = defined('FORCE_GZIP') ? gzcompress(serialize($OmniscientBeing),1) : serialize($OmniscientBeing);
		$GLOBALS["NOLOHGarbage"] = true;
		unset($OmniscientBeing, $GLOBALS["OmniscientBeing"]);
		unset($GLOBALS["NOLOHGarbage"]);
	}
	
	private function ExplodeDragCatch($objectsString)
	{
		$objs = array();
		$objectsIdArray = explode(",", $objectsString);
		$objectsCount = count($objectsIdArray);
		for($i=0; $i<$objectsCount; $i++)
			$objs[] = GetComponentById($objectsIdArray[$i]);
		return $objs;
	}
	
	private function ExplodeItems($optionsString)
	{
		$items = new ArrayList();
		$optionsArray = explode("~d3~", $optionsString);
		$optionsCount = count($optionsArray);
		for($i=0; $i<$optionsCount; $i++)
		{
			$option = explode("~d2~", $optionsArray[$i]);
			$items->Add(new Item($option[0], $option[1]));
		}
		return $items;
	}
	
	private function ExplodeSelectedIndices($indicesString)
	{
		return explode("~d2~", $indicesString);
	}
}

?>