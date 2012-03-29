function flashResizer(flId,width,height){ 
	if (document.getElementById(flId+"Container")){
		if (flId.indexOf("nav") == -1)
		{
			if (width != 0) document.getElementById(flId+"Container").style.width = width+"px";		
		}
		if (height != 0) document.getElementById(flId+"Container").style.height = height+"px";
	}
	if (document.getElementById(flId+"Flash")){
		if (width != 0) document.getElementById(flId+"Flash").style.width = width+"px";
		if (height != 0) document.getElementById(flId+"Flash").style.height = height+"px";
	}
	if (allowDebbuger)
	{
		var debug = document.getElementById("debuger");
		debug.className = "visible";
		debug.innerHTML = debug.innerHTML+flId+":"+width+":"+height+"<br />";
	}
	return "flashCall result:ok w:"+width+", h:"+height;
}