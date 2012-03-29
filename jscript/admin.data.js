//AJAX
var callBackFunction = "";
var modulId = -1;
var uniqId = -2;


if (window.XMLHttpRequest) {
	var req = new XMLHttpRequest();
} else if (window.ActiveXObject) {
	var req = new ActiveXObject("Microsoft.XMLHTTP");
}

function ajaxLoad(url, target) {
  document.getElementById(target).innerHTML = 'loading...';
  if (window.XMLHttpRequest) {
	req = new XMLHttpRequest();
  } else if (window.ActiveXObject) {
	req = new ActiveXObject("Microsoft.XMLHTTP");
  }
  if (req != undefined) {
	req.onreadystatechange = function() {ajaxDone(url, target);};
	req.open("GET", url, true);
	req.send("");
  }
}  

function ajaxDone(url, target) {
  if (req.readyState == 4) { // only if req is "loaded"
    if ((req.status == 200) || (req.status == 0)) { // only if "OK"
      document.getElementById(target).innerHTML = req.responseText;
	  eval(callBackFunction);
	  callBackFunction = "";
    } else {
      document.getElementById(target).innerHTML=" AHAH Error:\n"+ req.status + "\n" +req.statusText;
    }
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
  }
}

var linkOpened = "";
var defaultValue = ""

function linkWindow(dataId){
	linkOpened = dataId;
	
	if (window) window.scroll(0,0);
	
	if (document.documentElement)
	{
		document.documentElement.scrollTop = 0;
		document.documentElement.style.overflow = "hidden";
		
	} else if (document.body)
	{
		document.body.scrollTop = 0;
		document.body.style.overflow = "hidden";		
	}
	defaultValue = document.getElementById("linkId"+dataId).value;
	if (document.getElementById(dataId+"ListContainer"))
	{
		document.getElementById(dataId+"Mask").className = "dataLoaderIeMask visible";
		document.getElementById(dataId+"ListContainer").className = "dataLoader visible";
		var linkType = dataId;
		if (dataId.indexOf('_ID_') != -1)
		{
			var linkArr = dataId.split('_ID_');
			linkType = linkArr[0]+"&subLink="+linkArr[1];
		} 
		ajaxLoad("linked.php?type="+linkType,dataId+"ListContainer");
		callBackFunction = "updateCheckboxItems(\""+dataId+"\")";
	}
	if (document.getElementById("backgroundMask"))
	{
		document.getElementById("backgroundMask").className = "visible";
	}
}

function viewBranch(type,branchId){
	var linkType = type;
	if (type.indexOf('_ID_') != -1)
	{
		var linkArr = type.split('_ID_');
		linkType = linkArr[0]+"&subLink="+linkArr[1];
	} 
	if (document.getElementById("linkedBranchData"))
	{
		ajaxLoad("linked.php?type="+linkType+"&branchId="+branchId,"linkedBranchData");
		callBackFunction = "updateCheckboxItems(\""+type+"\")";
	}
}


function linkedSave(dataId){
	if (document.documentElement)
	{
		document.documentElement.style.overflow = "auto";
		document.documentElement.scrollTop = 0;
	} else if (document.body)
	{
		document.body.style.overflow = "auto";
		document.body.scrollTop = 0;
	}
	if (document.getElementById("linkId"+dataId))
	{
		var link_value = document.getElementById("linkId"+dataId);
		document.getElementById("debuger").innerHTML = link_value.value;
	}
	if (document.getElementById(dataId+"ListContainer"))
	{
		document.getElementById(dataId+"Mask").className = "dataLoaderIeMask hidden";
		document.getElementById(dataId+"ListContainer").className = "dataLoader hidden";
		document.getElementById(dataId+"ListContainer").innerHTML = "";
	}
	linkOpened = "";
	maskClose();
	buildLinkPreview(dataId,modulId,uniqId);
}

function linkedClose(dataId){
	if (document.documentElement)
	{
		document.documentElement.style.overflow = "auto";
		document.documentElement.scrollTop = 0;
	} else if (document.body)
	{
		document.body.style.overflow = "auto";
		document.body.scrollTop = 0;
	}
	if (document.getElementById("linkId"+dataId))
	{
		document.getElementById("linkId"+dataId).value = defaultValue;
		document.getElementById("debuger").innerHTML = document.getElementById("linkId"+dataId).value;
		defaultValue = "";
	}
	if (document.getElementById(dataId+"ListContainer"))
	{
		document.getElementById(dataId+"Mask").className = "dataLoaderIeMask hidden";
		document.getElementById(dataId+"ListContainer").className = "dataLoader hidden";
		document.getElementById(dataId+"ListContainer").innerHTML = "";
	}
	linkOpened = "";
	maskClose();
}

function linkItem(type,id){
	if (document.getElementById("linkId"+type))
	{
		var addItem=true;
		var link_value = document.getElementById("linkId"+type);
		if (link_value.value.indexOf("x"+id+"x") != -1){
			var re =new RegExp("x"+id+"x");
			 link_value.value = link_value.value.replace(re,'');
			 addItem=false
		} else {
			link_value.value = link_value.value+"x"+id+"x";
		}
		document.getElementById("debuger").innerHTML = link_value.value;
		switchItemCheckBox(type,id,addItem);
	}
}

function updateCheckboxItems(type){
	if (document.getElementById("linkId"+type))
	{
		var link_value = document.getElementById("linkId"+type);
		var re1 = new RegExp("xx","g");
		var str_id = link_value.value.replace(re1,",");
		str_id = str_id.replace(/x/g,"");
		var ids = str_id.split(",");
		for (var id in ids){
			switchItemCheckBox(type,ids[id],true);
		}
	}
}

function switchItemCheckBox(type,id,addItem){
	if (document.getElementById("check"+type+"-"+id))
	{
		var obj = document.getElementById("check"+type+"-"+id);
		if (addItem)
		{			
			obj.src = "i/ico/checkbox-a.gif";
		} else {
			obj.src = "i/ico/checkbox-n.gif";
		}
	}
}

function maskClose(){
	if (document.getElementById("backgroundMask"))
	{
		document.getElementById("backgroundMask").className = "hidden";
	}
	if (linkOpened != "")
	{
		linkedClose(linkOpened)
	}
}

function buildLinkPreview(dataId,modul,uId){
	if (modul != undefined)
	{
		modulId = modul;
	}
	if (uId != undefined)
	{
		uniqId = uId;
	}
	if (document.getElementById("linkId"+dataId))
	{
		var link_value = document.getElementById("linkId"+dataId);
		if (document.getElementById("debuger"))
		{
			document.getElementById("debuger").innerHTML = link_value.value;
		}
		if (link_value.value != "")
		{
			if (document.getElementById(dataId+"PreviewContainer"))
			{
				document.getElementById(dataId+"PreviewContainer").className = "dataPreview visible";
				if (req.readyState == 0)
				{
					var linkType = dataId;
					if (dataId.indexOf('_ID_') != -1)
					{
						var linkArr = dataId.split('_ID_');
						linkType = linkArr[0]+"&subLink="+linkArr[1];
					} 					
					ajaxLoad("linked.php?type="+linkType+"&do=preview&modul="+modulId+"&uniq_id="+uniqId+"&data="+link_value.value,dataId+"PreviewContainer");
				} else {
					//alert("waiting");
					var retry = setTimeout("buildLinkPreview(\""+dataId+"\","+modulId+","+uniqId+")", 200);
				}
			}
		} else {
			if (document.getElementById(dataId+"PreviewContainer"))
			{
				document.getElementById(dataId+"PreviewContainer").className = "dataPreview hidden";
				document.getElementById(dataId+"PreviewContainer").innerHTML = "";
			}
		}
	}
}

function linkDelete(id,type){
	if (document.getElementById("linkId"+type))
	{
		var link_value = document.getElementById("linkId"+type);
		if (link_value.value.indexOf("x"+id+"x") != -1)
		{
			var rem = new RegExp("x"+id+"x");
			link_value.value = link_value.value.replace(rem,"");
			if (document.getElementById("p_"+type+"x"+id))
			{
				document.getElementById("p_"+type+"x"+id).innerHTML = "";
				document.getElementById("p_"+type+"x"+id).className = "hidden";
			}
			if (link_value.value == "")
			{
				if (document.getElementById(type+"PreviewContainer"))
				{
					document.getElementById(type+"PreviewContainer").className = "dataPreview hidden";
					document.getElementById(type+"PreviewContainer").innerHTML = "";
				}
			}
		}
	}
}

function linkOrder(id,type,dir){
	if (document.getElementById("linkId"+type))
	{
		var link_value = document.getElementById("linkId"+type);
		var re1 = new RegExp("xx","g");
		var str_id = link_value.value.replace(re1,",");
		str_id = str_id.replace(/x/g,"");
		var ids = str_id.split(",");
		var index = -1;
		for (var i=0;i<ids.length ;i++)
		{
			if (ids[i]==id)
			{
				index = i
			}
		}
		var nearId = (index+dir);
		if ((nearId >= 0) && (nearId < ids.length))
		{
			if (index != -1)
			{
				var nearestId  = ids[nearId];
				ids[nearId] = id;
				ids[index] = nearestId;
			}
			var newOrder = "";
			for (var i=0;i<ids.length ;i++)
			{
				newOrder += "x"+ids[i]+"x";
			}
			link_value.value = newOrder;
			reorderPreview(type);
		}
	}
}

function reorderPreview(type){
	if (document.getElementById("linkId"+type))
	{
		var link_value = document.getElementById("linkId"+type);
		if (link_value.value != "")
		{
			var re1 = new RegExp("xx","g");
			var str_id = link_value.value.replace(re1,",");
			str_id = str_id.replace(/x/g,"");
			var ids = str_id.split(",");
			if (document.getElementById(type+"PreviewContainer"))
			{
				var previewBox = document.getElementById(type+"PreviewContainer");
				var itemDiv = previewBox.getElementsByTagName("div");
				var newOrderContent = "";
				for (var i=0;i<ids.length ;i++)
				{
					for (var d=0;d<itemDiv.length ;d++ )
					{
						var thisItem = itemDiv[d];
						var splitId = thisItem.id.split('x');
						if (splitId[1] == ids[i])
						{
							cls = "linkItemBox";
							if (type.indexOf("images") != -1 || type.indexOf("videos") != -1)
							{
								cls = "imageBox";
							} else if (type.indexOf("files") != -1) {
								cls = "linkedFile";
							} else if (type.indexOf("urls") != -1) {
								cls = "linkedUrl";
							} else {
								cls = "linkedItemRow";
							}
							newOrderContent += '<div class="'+cls+'" id="p_'+type+"x"+ids[i]+'">'+thisItem.innerHTML+'</div>';
						}
					}
				}
				newOrderContent += '<div class="clear"></div>';
				previewBox.innerHTML = newOrderContent;
			}
		}
	}
}

function editLinkDesc(id,type){
	if (document.getElementById("desc_"+type+"x"+id))
	{
		var linkDesc = document.getElementById("desc_"+type+"x"+id);
		linkDesc.className = "descText hidden";
	}
	if (document.getElementById("form_"+type+"x"+id))
	{
		var linkForm = document.getElementById("form_"+type+"x"+id);
		linkForm.className = "descInput";
	}
}

function saveLinkDesc(id,i_type,type,modul,uniq_id){
	if (document.getElementById("desc_"+type+"x"+id))
	{
		var linkDesc = document.getElementById("desc_"+type+"x"+id);
		linkDesc.className = "descText";
	}
	if (document.getElementById("form_"+type+"x"+id))
	{
		var linkForm = document.getElementById("form_"+type+"x"+id);
		linkForm.className = "descInput hidden";
	}
	if (document.getElementById("desct_"+type+"x"+id))
	{
		var desc = document.getElementById("desct_"+type+"x"+id);
		if (document.getElementById("input_"+type+"x"+id))
		{
			var inp = document.getElementById("input_"+type+"x"+id);
			if (desc.innerHTML != inp.value)
			{
				desc.innerHTML = inp.value;
				ajaxLoad("linked.php?do=save_desc&type="+i_type+"&modul="+modul+"&uId="+uniq_id,"debuger");
				callBackFunction = "descriptionSaved(\""+inp.value+"\")";
			}			
		}		
	}
}

function descriptionSaved(value){
	//alert("Desc saved: "+value);
}