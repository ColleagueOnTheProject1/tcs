
function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}
//
/**отправка формы*/
function sendForm(form, getStr){
	var fields = {};
	var json;
	var s = "";
	var key;
	for(var i = 0; i < form.elements.length; i++){
		if(form.elements[i].value != undefined){
			key = form.elements[i].name;
			if(!key){
				continue;
			}
			if(form.elements[i].type == 'radio' && form.elements[i].checked == false)
				continue;
			if(form.elements[i].type == 'checkbox' && form.elements[i].checked == false)
				continue;			
			if(form.elements[i].tagName="select" && form.elements[i].selectedIndex == 0)
				fields[key] = '';
			fields[key] = form.elements[i].value;
			s = s + '&' + key + '=' + fields[key];
		}
	}
	s = s.substr(1);
	if(!getStr){
		getStr = '';
	}else{
		getStr = '&' + getStr;
	}
	s = s + getStr;
	json = JSON.stringify(fields);
	var xmlhttp = getXmlHttp();
	xmlhttp.open("POST",'php/action.php',false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(s);
	parse(xmlhttp.responseText);
}
//отправка формы с бинарными данными
function sendFormData(form){
	function responseHandler(){
		if(xmlhttp.readyState == 4)
			parse(xmlhttp.responseText);
	}
	var formData = new FormData(form);
	var xmlhttp = getXmlHttp();
	xmlhttp.open("POST",'php/action.php',true);
	xmlhttp.onreadystatechange = responseHandler;
	xmlhttp.send(formData);
}
//

function sendAction(action, getStr){
	function responseHandler(){
		if(xmlhttp.readyState == 4)
			parse(xmlhttp.responseText);
	}
	var xmlhttp = getXmlHttp();
	xmlhttp.open("post",'php/action.php',true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.onreadystatechange = responseHandler;
	if(!getStr){
		getStr = '';
	}else{
		getStr = '&' + getStr;
	}
	xmlhttp.send('action='+action + getStr);
	//parse(xmlhttp.responseText);
}
/**отправляет запрос и при получении ответа вызывает заданный обработчик события*/
function sendRequest(get, handler){
	var xmlhttp = getXmlHttp();
	xmlhttp.open("GET",'php/action.php' + get,false);
	xmlhttp.send();	
	if(handler)
		handler(xmlhttp.responseText);
}
/**запрашивает данные из базы и обновляет текущие данные*/
function update(){
	function responseHandler(text){
		var connect_form;
		var base_form;
		data = JSON.parse(text);
		if(data.error == 1){//сервер не найден
			config = data;
			connect_form = document.getElementById("connect-form");
			connect_form.host.value = data['host'];
			connect_form.user.value = data['user'];
			connect_form.password.value = data['pass'];
			connect_form.style.display = "block";
		}
		else if(data.error == 2){//база не найдена
			config = data;
			base_form = document.getElementById("base-form");
			base_form.getElementsByClassName("c")[0].innerHTML = "База данных с именем <b>"+data['base'] + "</b> не найдена";
			base_form.base.value = data['base'];
			base_form.style.display = "block";
		}else
			sort(SORT_PRIORITY);
	}
	document.getElementById("sureface").style.display = "block";	
	sendRequest("?name=get_tasks", responseHandler);
	document.getElementById("sureface").style.display = "none";
}
/**Удаляет выбранные строки из базы*/
function removeSelectedFields(){
	function completeHandler(data){
		update();
	}
	var ids=[];
	for (var i = 0; i < data.length; i++ ){
		if(tasks.rows[i + 1].cells[3].getElementsByTagName("input")[0].checked){
			ids[ids.length] = data[i].id;
		}
	}
	sendRequest("?name=remove_tasks&ids=" + ids.join(","), completeHandler);
}

/**перезаписывает строку таблицы в базу*/
function addTask(){
	function completeHandler(data){
		update();
	}
}
/**собирает строку из массива для get запроса*/
function getRString(arr){
	var s = "";
	var w;
	for (var w in arr ){
		s = s + w + "=" + arr[w] + "&";
	}
	s = s.slice(0, s.length - 1);
	return s;
}
/**перезаписывает строку таблицы в базе*/
function editTaks(){
	var i = 0;
	function completeHandler(data){
		update();
	}
	sendRequest("?name=edit_task&" + getRString(temp_data), completeHandler);
}
/**создание базы*/
function createBase(){
	base_form.style.display = "none";
	sendRequest("?name=create_base", update);	
}
/***/
function setConfig(){
	var connect_form;
	var base_form;
	if(config.error == 1){
		connect_form = document.getElementById("connect-form");
		connect_form.style.display = "none";
		config['host'] = connect_form.host.value;
		config['user'] = connect_form.user.value
		config['pass'] = connect_form.password.value
	}else{
		base_form = document.getElementById("base-form");
		base_form.style.display = "none";
		config['base'] = base_form.base.value;
	}
	sendRequest("?name=config&" + getRString(config), update);
}

/***/