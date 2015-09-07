/*скрипт распределяет задачи между скриптами. подключается в последнюю очередь*/
//события------------
const ACTION_CONNECT="connect_error";//имя события - не удалось подключится к серверу
const ACTION_BASE="base_error";//имя события - не удалось подключится к базе
const ACTION_GET_TASKS="get_tasks";//имя события - получить данные задачи
const ACTION_GET_USERS="get_users";//имя события - список пользователей с дополнительной информацией
const ACTION_GET_GROUPS="get_groups";//имя события - группы
const ACTION_AUTH="auth";//имя события - авторизация
const ACTION_TASK_IMAGE="task_image";//имя события - картинка сохранена на сервер
//------------события
var cookie;
var tabs;
var tasks;
var groups;
var users;
var cur_task;
function init(){
	
	parser_handlers[ACTION_CONNECT] = showConnectForm;
	parser_handlers[ACTION_BASE] = showBaseForm;
	parser_handlers[ACTION_GET_TASKS] = showTasks;
	parser_handlers[ACTION_GET_GROUPS] = showGroups;
	parser_handlers[ACTION_AUTH] = showLoginForm;
	parser_handlers[ACTION_GET_USERS] = showUsers;
	parser_handlers[ACTION_TASK_IMAGE] = taskAddImage;
	
	cookie = cookieToArr();
	if(!cookie['login'] || !cookie['password']){
		showLoginForm();
	}else {
		showLogin();
		sendAction(ACTION_AUTH);
	}
}
//инициализация обработчиков событий
function initListeners(){
	tabs = document.getElementsByClassName('tab1');
	for (var i = 0; i < tabs.length; i++)	{
		tabs[i].addEventListener('click', getData);
	}
}
//отменяет последнюю авторизацию
function exit(){
	removeCookie('login');
	removeCookie('password');
	location.reload();
}
//отображает имя авторизованного пользователя
function showLogin(){
	document.getElementById("user").style.display = 'block';
	cookie = cookieToArr();
	document.getElementById("login").innerHTML = cookie['login'];
	document.getElementById("exit").innerHTML = cookie['login'];
}
document.addEventListener('DOMContentLoaded', function() {
	init();
	initListeners();
});