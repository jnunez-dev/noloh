/*! Copyright (c) 2005 - 2008 NOLOH, LLC. All rights reserved */

SavedControls = new Array();
NOLOHChanges = new Object();
NOLOHKey = null;
NOLOHCaught = new Array();
_NFocus = null;
_NContextMenuSource = null;
_NFlashArgs = null;
_NShiftObjArray = null;
ConversionArray = new Object();
ConversionArray["style.left"] = "Left";
ConversionArray["style.top"] = "Top";
ConversionArray["style.width"] = "Width";
ConversionArray["style.height"] = "Height";
ConversionArray["style.zIndex"] = "ZIndex";
ConversionArray["style.background"] = "BackColor";
ConversionArray["style.color"] = "Color";
ConversionArray["value"] = "_NText";
ConversionArray["newText"] = "_NText";
ConversionArray["selectedIndex"] = "SelectedIndex";
ConversionArray["selectedTab"] = "SelectedTab";
ConversionArray["checked"] = "Checked";
ConversionArray["src"] = "Src";
ConversionArray["scrollLeft"] = "ScrollLeft";
ConversionArray["scrollTop"] = "ScrollTop";
ConversionArray["options"] = "_NItems";
ConversionArray["selectedIndices"] = "_NSelectedIndices";
ConversionArray["calViewDate.setMonth"] = "ViewMonth";
ConversionArray["calViewDate.setFullYear"] = "ViewYear";
ConversionArray["calSelectDate.setDate"] = "Date";
ConversionArray["calSelectDate.setMonth"] = "Month";
ConversionArray["calSelectDate.setFullYear"] = "Year";
NOLOHUpload = new Object();
NOLOHUpload.FileUploadObjIds = new Array();
NOLOHVisit = -1;
HighestZIndex = 0;
LowestZIndex = 0;

function _NInit(loadLblId, loadImgId)
{
	document.body.NOLOHPostingBack = false;
	NOLOHCatchers = Array();
	window.onscroll = BodyScrollState;
	window.onresize = BodySizeState;
	_NLoadLbl = loadLblId;
	_NLoadImg = loadImgId;
	_NSetProperty(document.body.id, "Width", document.documentElement.clientWidth);
	_NSetProperty(document.body.id, "Height", document.documentElement.clientHeight);
	var Graveyard = document.createElement("DIV");
	Graveyard.id = "Graveyard";
	Graveyard.style.display = "none";
	document.body.appendChild(Graveyard);
	_NHash = location.hash;
	_NURLCheck = setInterval('CheckURL()', 500);
}

function CheckURL()
{
	if(_NHash != location.hash && (location.hash=="" || location.hash.charAt(1)=="/") && (_NHash=="" || _NHash.charAt(1)=="/"))
		location.reload(true);
}

function _NSetURL(hash)
{
	location = document.URL.split('#',1)[0] + "#/" + hash;
	_NHash = location.hash;
}

function SaveControl(id)
{
	var temp = _N(id);
	SavedControls[id] = temp.cloneNode(false);
	SavedControls[id].selectedIndex = temp.selectedIndex;
	SavedControls[id].checked = temp.checked;
	SavedControls[id].SelectedTab = temp.SelectedTab;
	SavedControls[id].selectedIndices = ImplodeSelectedIndices(SavedControls[id].options);
	if(temp.calViewDate != null)
	{
		SavedControls[id].calViewDate = new Object();
		SavedControls[id].calSelectDate = new Object();
		SavedControls[id].calViewDate.setMonth = temp.calViewDate.getMonth();
		SavedControls[id].calViewDate.setYear = temp.calViewDate.getYear();
		SavedControls[id].calSelectDate.setDate = temp.calSelectDate.getDate();
		SavedControls[id].calSelectDate.setMonth = temp.calSelectDate.getMonth();
		SavedControls[id].calSelectDate.setYear = temp.calSelectDate.getYear();
	}
}
function _NSetProperty(id, property, value)
{
	NOLOHChange(id, property, value);
	_NSave(id, property, value);
}

function NOLOHChangeInit(id, propertyString)
{
	if(NOLOHChanges[id] == null)
		NOLOHChanges[id] = new Object();
	if(NOLOHChanges[id][propertyString] == null)
		NOLOHChanges[id][propertyString] = new Object();	
}

function NOLOHChange(id, propertyString, newValue)
{
	var obj;
	obj = _N(id);
	if(!obj)
		obj = window[id];
	NOLOHChangeByObj(obj, propertyString, newValue);
}

function NOLOHChangeByObj(obj, propertyString, newValue)
{
	if(obj == null)
		return;
	switch(propertyString)
	{
		case "calViewDate.setMonth":
		case "calViewDate.setFullYear":
		case "calSelectDate.setDate":
		case "calSelectDate.setMonth":
		case "calSelectDate.setFullYear":
			eval("obj." + propertyString + "(newValue);");
			break;
		case "KeyPress":
		case "ReturnKey":
		case "TypePause":
			obj.onkeypress = function(event)
			{
				_NSave(obj.id,'value',obj.value);
				if(obj.ReturnKey != null && event.keyCode == 13)
					obj.ReturnKey.call(this, event);
				if(obj.KeyPress != null)
				{
					NOLOHKey = Math.max(event.keyCode, event.charCode);
					obj.KeyPress.call(this, event);
				}
				if(obj.TypePause != null && (event.keyCode < 37 || event.keyCode > 40))
				{
					clearTimeout(obj.TypePauseTimeout);
					obj.TypePauseTimeout = setTimeout("var obj = _N('"+obj.id+"'); _NSave(obj.id,'value',obj.value); obj.TypePause.call();", 500);
				}
			}
		case "onblur":
		case "onchange":
		case "onclick":
		case "ondblclick":
		case "onfocus":
		case "onelapsed":
		case "oninput":
		case "onmouseout":
		case "onmouseover":
		case "onmouseup":
		case "onload":
		case "onpaste":
		case "onscroll":
        case "onunload":
			eval("obj." + propertyString + " = function(event) {" + newValue + ";}");
			break;
		case "oncontextmenu":
			eval("obj.oncontextmenu = function(event) {" + newValue + "; if(obj.ContextMenu!=null) ShowContextMenu(event, obj); return false;}");
			break;
		case "onmousedown":
			eval("obj.onmousedown = function(event) {" + newValue + "; if(obj.Shifts!=null && obj.Shifts.length!=0 && _NShiftObjArray==null) ShiftStart(event, obj.Shifts);}");
			break;
		case "DragCatch":
			if(newValue == "")
			{
				for(var i=0; i<NOLOHCatchers.length; ++i)
					if(NOLOHCatchers[i] == obj.id)
					{
						NOLOHCatchers.splice(i, 1);
						break;
					}
			}
			else
				NOLOHCatchers.push(obj.id);
			eval("obj.DragCatch = function(event) {" + newValue + ";}");
			break;
		case "href":
			obj.href = newValue=="#" ? "javascript:void(0);" : newValue;
			break;
		case "Shifts":
			if(obj.onmousedown == null)
				NOLOHChangeByObj(obj, "onmousedown", "");
		case "ChildrenArray":
			eval("obj." + propertyString + " = " + newValue + ";");
			break;
		case "ContextMenu":
			if(obj.oncontextmenu == null)
				NOLOHChangeByObj(obj, "oncontextmenu", "");
				obj.ContextMenu = newValue;
			break;
		case "Group":
			obj.Group = window[newValue];
			if(newValue)
				obj.Group.Elements.push(obj.id);
			break;
		case "Selected":
			if(obj.Selected != newValue)
			{
				obj.Selected = newValue;
				if(obj.Group!=null && obj.Group.onchange!=null && !document.body.NOLOHPostingBack)
				{
					_NSave(obj.id,'Selected',newValue);
					obj.Group.onchange.call();
				}
			}
			break;
		case "style.zIndex":
			if(newValue > HighestZIndex)
				HighestZIndex = newValue;
			if(newValue < LowestZIndex)
				LowestZIndex = newValue;
			obj.style.zIndex = obj.BuoyantParentId == null ? newValue : newValue + 9999;
			break;
		case "style.left":
			if(obj.BuoyantParentId == null)
			{
				obj.style.left = newValue;
				if(obj.BuoyantChildren != null)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						MoveBuoyant(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantLeft = parseInt(newValue);
				MoveBuoyant(obj.id);
			}
			break;
		case "style.top":
			if(obj.BuoyantParentId == null)
			{
				obj.style.top = newValue;
				if(obj.BuoyantChildren != null)
					for(var i=0; i<obj.BuoyantChildren.length; ++i)
						MoveBuoyant(obj.BuoyantChildren[i]);
			}
			else
			{
				obj.BuoyantTop = parseInt(newValue);
				MoveBuoyant(obj.id);
			}
		default:
			eval("obj." + propertyString + " = newValue;");
	}
}

function _NSave(id, propertyString, newValue)
{
	if(id.indexOf("_") >= 0)
		return;
	var tempObj;
	if(propertyString != "timer")
		tempObj = _N(id);
	else
		eval("tempObj = window." + id + ";");
	if(typeof newValue == "undefined")
		eval("newValue = tempObj."+propertyString+";");
	switch(propertyString)
	{
		case "value":
			NOLOHChangeInit(id, "value");
			NOLOHChanges[id][propertyString][0] = (typeof newValue == "string" ? newValue.replace(/&/g, "~da~").replace(/\+/g, "~dp~") : newValue);
			break;
		case "style.left":
		case "style.top":
		case "style.width":
		case "style.height":
			NOLOHChangeInit(id, propertyString);
			NOLOHChanges[id][propertyString][0] = parseInt(newValue);
			break;
		case "style.visibility":
		case "style.display":
			NOLOHChangeInit(id, "Visible");
			var obj = _N(id);
			NOLOHChanges[id]["Visible"][0] = obj.style.display=="none" ? "null" : (obj.style.visibility == "inherit");
			break;
		case "style.opacity":
			NOLOHChangeInit(id, "Opacity");
			NOLOHChanges[id]["Opacity"][0] = newValue * 100;
			break;
		default:
			NOLOHChangeInit(id, propertyString);
			NOLOHChanges[id][propertyString][0] = typeof newValue == "boolean" ? (newValue ? 1 : 0) : newValue;
	}
}

function ScrollState(id)
{
	var obj = _N(id);
	_NSetProperty(id,"scrollLeft",obj.scrollLeft);
	_NSetProperty(id,"scrollTop",obj.scrollTop);
}

function BodyScrollState()
{
	var X = Math.max(document.body.scrollLeft, document.documentElement.scrollLeft)+1;
	var Y = Math.max(document.body.scrollTop, document.documentElement.scrollTop)+1;
	var loadImg = _N(_NLoadImg);
	loadImg.style.left = X+"px";
	loadImg.style.top = Y+"px";	
	var loadLbl = _N(_NLoadLbl);
	loadLbl.style.left = X+30+"px";
	loadLbl.style.top = Y+6+"px";
}

function BodySizeState()
{
	var body = document.body;
	if(body.ShiftsWith != null)
	{
		var deltaX = document.documentElement.clientWidth - body.Width;
		var deltaY = document.documentElement.clientHeight - body.Height;
		SetShiftWithInitials(body);
		ShiftObjects(body.ShiftsWith, deltaX, deltaY);
	}
	if(body.BuoyantChildren != null)
	{
		var buoyantCount = body.BuoyantChildren.length;
		for(var i=0; i<buoyantCount; ++i)
			MoveBuoyant(body.BuoyantChildren[i]);
	}
	_NSetProperty(body.id, "Width", document.documentElement.clientWidth);
	_NSetProperty(body.id, "Height", document.documentElement.clientHeight);
}

function AddOptionAndSave(id, option)
{
	var tempObj = _N(id);
	tempObj.options.add(option);
	_NSave(id, "options", ImplodeOptions(tempObj.options));
}

function RemoveOptionAndSave(id, index)
{
	var tempObj = _N(id);
	tempObj.remove(index);
	_NSave(id, "options", ImplodeOptions(tempObj.options));
}

function _NSetP(id, nameValuePairs)
{
	var i = 0;
	var obj = _N(id);
	while(i<nameValuePairs.length)
	{
		NOLOHChangeByObj(obj, nameValuePairs[i], nameValuePairs[i+1]);
		SavedControls[id][nameValuePairs[i++]] = nameValuePairs[i++];
	}
}

function _NSetPEvtee(id, nameValuePairs)
{
	var i = 0;
	var obj = _N(id);
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(obj, nameValuePairs[i++], nameValuePairs[i++]);
}

function _NAdd(addTo, tag, nameValuePairs, beforeId)
{
	var elt = document.createElement(tag);
	elt.style.position = "absolute";
	var i = 0;
	while(i<nameValuePairs.length)
		NOLOHChangeByObj(elt, nameValuePairs[i++], nameValuePairs[i++]);
	addTo = _N(addTo);
	if(typeof beforeId == "undefined")
		addTo.appendChild(elt);
	else
	{
		var before = _N(beforeId);
		if(before && before.parentNode == addTo)
			addTo.insertBefore(elt, before);
		else
			addTo.appendChild(elt);
	}
	SaveControl(elt.id);
}

function _NAdopt(id, parentId)
{
    var ele = _N(id);
    ele.parentNode.removeChild(ele);
    _N(parentId).appendChild(ele);
}

function _NRem(id)
{
	var ele = _N(id);
	ele.parentNode.removeChild(ele);
	_N("Graveyard").appendChild(ele);
    if(ele.BuoyantChildren != null)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRem(ele.BuoyantChildren[i]);
}

function _NRes(id, parentId)
{
	var ele = _N(id);
	_N("Graveyard").removeChild(ele);
	_N(parentId).appendChild(ele);
    if(ele.BuoyantChildren != null)
    	for(var i=0; i<ele.BuoyantChildren.length; ++i)
			_NRes(ele.BuoyantChildren[i], parentId);
}

function _NAsc(id)
{
	var ele = _N(id);
	if(ele)
    {
		ele.parentNode.removeChild(ele);
        if(ele.BuoyantChildren != null)
        	for(var i=0; i<ele.BuoyantChildren.length; ++i)
    			_NAsc(ele.BuoyantChildren[i]);
    }
}

function _NGCAsc(idArr)
{
	var arrLength = idArr.length;
	for(var i=0; i<arrLength; ++i)
		_NAsc(idArr[i]);
}

function GetChanges()
{
	var changes = "", distinctId, property;
	for(distinctId in NOLOHChanges)
	{
		changes += distinctId;
		for(property in NOLOHChanges[distinctId])
			if(NOLOHChanges[distinctId][property][0] != SavedControls[distinctId][property])
			{
				changes += "~d1~";
				SavedControls[distinctId][property] = NOLOHChanges[distinctId][property][0];
				changes += (ConversionArray[property] ? ConversionArray[property] : property) + "~d1~" + NOLOHChanges[distinctId][property][0];
			}
		changes += "~d0~";
	}
	NOLOHChanges = new Object();
	return changes.substring(0,changes.length-4);
}

function ExecReqResponse(response)
{
	if(response[0] != "")
	{
		var s = document.createElement("SCRIPT");
		s.type = "text/javascript";
		s.text = response[0];
		document.getElementsByTagName('head')[0].appendChild(s);
		eval(response[0]);
	}
	eval(response[1]);
}

function CompleteReqResponse(tmpLoadImg, tmpLoadLbl)
{
	_NLoadImg = tmpLoadImg;
	_NLoadLbl = tmpLoadLbl;
	_N(_NLoadImg).style.visibility = "hidden";
	_N(_NLoadLbl).style.visibility = "hidden";
	document.body.NOLOHPostingBack = false;
}

function ProcessReqChange()
{
	var ready=req.readyState;
	var data=null;
	if (ready==4)
	{
   		var response = req.responseText.split("/*~NScript~*/", 2);
        var tmpLoadImg = _NLoadImg;
        var tmpLoadLbl = _NLoadLbl;
		if(typeof _NDebugMode == "undefined")
		{
			ExecReqResponse(response);
			CompleteReqResponse(tmpLoadImg, tmpLoadLbl);
		}
		else
	   		try
	   		{
				ExecReqResponse(response);
	   		}
	   		catch(err)
	   		{
				alert(_NDebugMode ? "A javascript error has occurred:\n\n" + err.name + "\n" + err.description : "An application error has occurred.");
	   		}
	        finally
	        {
				CompleteReqResponse(tmpLoadImg, tmpLoadLbl);
	        }
	}
}

function PostBack(EventType, ID, event)
{
	if(!document.body.NOLOHPostingBack)
	{
		document.body.NOLOHPostingBack = true;
		var str = "NOLOHClientChanges="+GetChanges()+"&NOLOHServerEvent="+EventType+"@"+ID+"&NOLOHVisit="+ ++NOLOHVisit;
		if(event != null)
			str += "&NOLOHMouseX="+event.pageX+"&NOLOHMouseY="+event.pageY;
		if(NOLOHKey != null)
		{
			str += "&NOLOHKey="+NOLOHKey;
			NOLOHKey = null;
		}
		if(NOLOHCaught.length != 0)
			str += "&NOLOHCaught="+NOLOHCaught.join(",");
        if(_NFocus != null)
        {
            var obj = _N(_NFocus);
            try
            {
                str += "&NOLOHFocus="+_NFocus+"&NOLOHSelectedText="+obj.value.substring(obj.selectionStart, obj.selectionEnd);
            }
            catch(err)
            {
                _NFocus = null;        
            }
        }
		if(_NContextMenuSource != null)
			str += "&NOLOHContextMenuSource="+_NContextMenuSource.id;
		if(_NFlashArgs != null)
		{
			str += "&NOLOHFlashArgs="+_NFlashArgs;
			_NFlashArgs = null;
		}
	    req = new XMLHttpRequest();
		_N(_NLoadImg).style.visibility = "visible";
		_N(_NLoadLbl).style.visibility = "visible";
        if(EventType != "Unload")
    	    req.onreadystatechange = ProcessReqChange;
	    req.open("POST", window.location.href, true);
	    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    req.setRequestHeader('Remote-Scripting', 'NOLOH-Postback');
	    req.send(str);
	}
}

function ReadyBox(id)
{
	if(NOLOHUpload.FileUploadObjIds.length > 0)
	{
		_N(id).UploadComplete = true;
		for(var i=0; i<NOLOHUpload.FileUploadObjIds.length; i++)
			if(_N(NOLOHUpload.FileUploadObjIds[i]).UploadComplete == false)
				return;
		PostBack(NOLOHUpload.EventType, NOLOHUpload.ID, NOLOHUpload.event);
		NOLOHUpload = new Object();
		NOLOHUpload.FileUploadObjIds = new Array();
	}
}

function PostBackWithUpload(EventType, ID, FileUploadObjIds, event)
{
	NOLOHUpload.EventType = EventType;
	NOLOHUpload.ID = ID;
	NOLOHUpload.FileUploadObjIds = FileUploadObjIds;
	NOLOHUpload.event = event;
	for(var i=0; i<FileUploadObjIds.length; i++)
	{
		iFrame = _N(FileUploadObjIds[i]);
		iFrame.UploadComplete = false;
		iFrame.contentWindow._N("frm").submit();
	}
}

function ImplodeOptions(OptionsArray)
{
	var tempString ="";
	for(var i=0; i<OptionsArray.length; i++)
	{
		tempString += OptionsArray[i].value + "~d2~";
		tempString += OptionsArray[i].text + "~d3~";
	}
	
    tempString = tempString.substring(0,tempString.length-4);
	return tempString;
}

function ImplodeSelectedIndices(OptionsArray)
{
	var retString = "";
	if(OptionsArray != null)
		for(var i=0; i < OptionsArray.length; i++)
			if(OptionsArray[i].selected)
				retString += i + "~d2~";
	retString = retString.substring(0,retString.length-4);
	return retString;
}