function signUp ()
{
	let url = `http://localhost:8080/php/server.php?signup=1`;
	let uidVal = uid.value.replaceAll(" ","");
	let pswrdVal = pswrd.value.replaceAll(" ","");
	if(uidVal.length < 1 || pswrdVal.length < 1)
	{
		slideup(snackbar, "Username or Password cannot be Empty !");
		return;
	}
	
    ajax(url, {uid : uidVal , pswrd : pswrdVal}, function (){
		if(this.readyState == 4 && this.status == 200){
			let data = JSON.parse(this.responseText);
			if(data.status == 200){
				snackbar.style.background = "#0097FF";
				slideup(snackbar, data.snackbar);
			}
			else{
				let data = JSON.parse(this.responseText);
				snackbar.style.background = "#B60014";
				//cl("dd : "+data.snackbar_error);
				slideup(snackbar, data.snackbar_error);
			}
		}
	}, "POST");
	//uid.value = pswrd.value = ""
}

function hasuid ()
{
	if(uid.value.length < 4)
	{
		uid_info.style.visibility = "hidden";
		return;
	}

	let url = `http://localhost:8080/php/server.php`;
	ajax(url, {hasuid : uid.value}, function (){
		if(this.readyState == 4 && this.status == 200){
			//cl("inside Ajax");
			let data = JSON.parse(this.responseText);
			if(data.status == 200){
				//cl("Inside 200")
				uid_info.innerHTML = data.uid_info;
				uid_info.style.visibility = "visible";
				uid_info.style.color = "#0097FF";
				uid.style.borderBottomColor = "green";
			}
			else
			if(data.status >= 400){
				if(data.uid_info){
					uid_info.innerHTML = data.uid_info;
					uid_info.style.visibility = "visible";
					uid.style.borderBottomColor = "#FF0004";
				}
				else{
					uid_info.style.visibility = "hidden";
					uid.style.borderBottomColor = "#333"
				}
				uid_info.style.color = "#FF0004";
			}
		}
	}, "GET");
}

function login ()
{
	let url = `http://localhost:8080/php/server.php?login=1`;
	let uidVal = uid.value.replaceAll(" ","");
	let pswrdVal = pswrd.value.replaceAll(" ","");
	if(uidVal.length < 1 || pswrdVal.length < 1)
	{
		slideup(snackbar, "Username or Password cannot be Empty !");
		return;
	}
	
    ajax(url, {uid : uidVal , pswrd : pswrdVal}, function (){
		if(this.readyState == 4 && this.status==200){
			let data = JSON.parse(this.responseText);
			if(data.status == 200)
				slideup(snackbar, data.snackbar);
			else
				slideup(snackbar, data.snackbar, {background : "#C1000B"});
		}
	}, "POST");
	//uid.value = pswrd.value = ""
}

function clearData ()
{
	let url = `http://localhost:8080/php/server.php`
    ajax(url, {clear : true}, function (){
		if(this.readyState == 4 )
			data.innerHTML += this.responseText;
	});
}

function showData ()
{
	let url = `http://localhost:8080/php/server.php`
    ajax(url, {show : true}, function (){
		if(this.readyState == 4)
			data.innerHTML += this.responseText;
	});
}

function cls(tag)
{	tag.innerHTML = ""; 	}

function togglePswrd(tag) {
  if(tag.checked)
  	pswrd.type = "text";
  else
	  pswrd.type = "password";
}

function slidedwn(tag) {
	//cl("inside slide down : "+ tag.id);

  tag.style.transition = '0.3s';
  tag.style.bottom = '-2.5rem';
  setTimeout(function (){
  	tag.style.display = "none";
  }, 310);
}

function slideup (tag, inrhtm, styles) {
  if(inrhtm)
   tag.innerHTML = inrhtm;

	css(tag, {
			transition : "0.2s",
			display : "block",
			width : window.innerWidth - 30 + "px",
			bottom : "0rem"
		});
	if(styles)
  	css(tag, styles);
  setTimeout(function (){
  	slidedwn( tag);
  }, 3000);
}
