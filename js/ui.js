/**скрипт для работы с интерфейсом*/
//показать форму настроек сервера
function showConnectForm(){
	document.getElementById("connect-form").style.display = "block";
}
//показать форму настроек базы
function showBaseForm(){
	document.getElementById("base-form").style.display = "block";
}
//
function showLoginForm(){
	if(cookie['login']){
		document.getElementById('login-form').login = cookie['login'];
		document.getElementById('login-form').password = cookie['password'];
		getData();
	}else
		document.getElementById('login-form').style.display = "block";	
	
}
function showTasks(){
	
}
function showUsers(){
	
}