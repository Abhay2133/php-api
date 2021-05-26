function ajax (url, obj,onReadyStateChanged, method="GET")
	{
	var xhttp = new XMLHttpRequest();
	if(method == "GET" && obj)
	{
		url += '?';
		for(let key in obj)
			url += `${key}=${obj[key]}&`;
		url = url.substring(0, url.length - 1)
	}
	else
		obj = JSON.stringify(obj)
	xhttp.open(method, url, true);
	if(method == "POST")
	{
		xhttp.setRequestHeader("Content-type", "application/json");
		xhttp.send(obj);
	}
	else
		xhttp.send();
	
	if(onReadyStateChanged)
		xhttp.onreadystatechange = onReadyStateChanged
	else
		xhttp.onreadystatechange = async function (){
		if( this.readyState == 4 )
		{
			return this.responseText;
		}
	}
}