function showDiv(id){
	if (id == "reply")
	{
		if (document.getElementById("reply"))
		{
			var reply = document.getElementById("reply");
			var inp = reply.getElementsByTagName("input");
			for (a=0;a<inp.length ; a++)
			{
				if (inp[a].name == "subject")
				{
					inp[a].value = "";
				}
				if (inp[a].name == "parentId")
				{
					inp[a].value = 0;
				}
			}
		}

	}



	if (document.getElementById("resMsg"))
	{
		res = document.getElementById("resMsg");
		res.className= "hidden";
	}
	div = document.getElementById(id);
	div.className= "visible";
}

function unhideTreeMenu(id){
	$("#"+id).removeClass('hidden');
	$("#"+id).show();
}


function unhideDiv(id){
	$("#"+id).removeClass('hidden');
	$("#"+id).show();
}

function hideDiv(id){
	$("#"+id).hide();
}

function mailParse(email)
{
	e = new String(email);
	e = e.replace("[at]","@").replace("[dot]",".").replace("[dot]",".");
	window.location = "mailto:"+e;
}

var prevBox = "";

function showEditBox(id){
	if (prevBox != id)
	{
		hideEditBox(prevBox);
	}
	if (document.getElementById(id))
	{
		editBox = document.getElementById(id);
		editBox.className= "editBox visible";
		prevBox = id;
	}
}

function hideEditBox(id){
	if (document.getElementById(id))
	{
		editBox = document.getElementById(id);
		editBox.className= "editBox hidden";
	}
}

function addModul() {
	if (document.getElementById("modulForm"))
	{
		document.getElementById("modulForm").className="inline";
	}
}