/*скрипт распределяет задачи между скриптами. подключается в последнюю очередь*/
const STATES = ['не начата','начата', 'приостановлена','на проверке', 'переоткрыта','закрыта'];
const PRIORITIES = ['низкий', 'средний', 'высокий', 'наивысший'];
const TASK_TYPES = ['новинка', 'улучшение', 'ошибка', 'тест', 'другое']//типы зыдач
const TASK_OTHER_TYPE_ID = 100;
const TASK_ALL_TYPES_ID = 127;
const TASK_ALL_TYPES = 'все';
//коды ошибок
const ERROR_USER_INCORRECT = 1;//не подходящее имя пользователя
const ERROR_USER_EXISTS = 2;//пользователь с таким именем уже существует
const ERROR_GROUP_REMOVE = 3;//
//сообщения при ошибках в соответствии с кодами ошибок
const ERROR_MESSAGES = {
	1:'не корректное имя пользователя', 
	2:'пользователь с таким именем уже существует',
	3:'удалить группу может только владелец или администратор'};
//события------------
const ACTION_CONNECT="connect";//имя события - подключение к серверу
const ACTION_BASE="base_error";//имя события - не удалось подключится к базе
const ACTION_GET_TASKS="get_tasks";//имя события - получить данные задачи
const ACTION_GET_COMPLETE_TASKS="get_complete_tasks";//имя события - получить список завершенных задач
const ACTION_GET_USERS="get_users";//имя события - список пользователей с дополнительной информацией
const ACTION_GET_GROUPS="groups";//имя события - получение группы
const ACTION_AUTH="auth";//имя события - авторизация
const ACTION_TASK_IMAGE="task_image";//имя события - картинка сохранена на сервер
const ACTION_GET_INFO="info";//имя события - получить id групп, задач и пользователей
const ACTION_ADD_USER="add_user";//имя события - добавить пользователя
const ACTION_ADD_GROUP="add_group";//имя события - добавить группу
const ACTION_REMOVE_USER="remove_user";//имя события - удалить пользователей
const ACTION_TASKS_INFO="tasks_info";//имя события - пришла информация о задачах
const ACTION_U_TASK_COUNT="u_task_count";//имя события - пришла информация о количестве задач текущего пользователя
const ACTION_EXPORT="export";//имя события - экспорт базы
const ACTION_ERROR="error";//имя события - ошибка
const ACTION_VERSION="version";//имя события - обновить номер версии



//------------события
var cookie;
var tabs;
var tasks;//данные задач
var task_status = 0;//1 - задачи получены, 2 - задача обновлена, 3 - задача создана, 4 - задача закрыта
var groups;
var users;
var cur_task;
var active_task;//активная задача в списке задач
var active_group;//активная группа в списке задач
var last_task_id;//id последней выбранной задачи в списке задач
var info = {};
var filter = false;
function init(){	
	parser_handlers[ACTION_CONNECT] = showConnectForm;
	parser_handlers[ACTION_BASE] = showBaseForm;
	parser_handlers[ACTION_GET_TASKS] = showTasks;
	parser_handlers[ACTION_GET_GROUPS] = groupsUpdate;
	parser_handlers[ACTION_AUTH] = showLoginForm;
	parser_handlers[ACTION_GET_USERS] = showUsers;
	parser_handlers[ACTION_TASK_IMAGE] = taskAddImage;
	parser_handlers[ACTION_GET_INFO] = getInfo;
	parser_handlers[ACTION_U_TASK_COUNT] = tasksInfoUpdate;
	parser_handlers[ACTION_EXPORT] = download;
	parser_handlers[ACTION_ERROR] = errorAlert;
	parser_handlers[ACTION_VERSION]=versionUpdate;
	
	beforeSend=showSend;
	afterSend=hideSend;
    sendAction(ACTION_CONNECT);
}
/**получить данные для текущей вкладки - пользователи/задачи/группы/...*/
function getData(use_filter){
	if(use_filter)
		filter = true;
	for (var i = 0; i < tabs.length; i++)	{
		if(tabs[i].checked == true)
			break;
	}
	switch(tabs[i].id){
		case 'tasks-tab':
				sendAction(ACTION_GET_TASKS, getFiltersStr());
			break;
		case 'users-tab':
					sendAction(ACTION_GET_USERS);
				break;
		case 'groups-tab':
				sendAction(ACTION_GET_GROUPS);
			break;
		default:;
	}
}
//инициализация обработчиков событий
function initListeners(){
	var objs;
	var i;
	//сброс информации о последней выбранной задаче
	function resetLastTask(){
		last_task_id = null;;
	}
	tabs = document.getElementsByClassName('tab1');
	for (i = 0; i < tabs.length; i++){
		tabs[i].addEventListener('click', getData);
	}
	objs = document.forms["filters"].getElementsByTagName('select');
	for (i = 0; i < objs.length; i++){
		objs[i].addEventListener('change',resetLastTask);
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
	document.getElementById("login").innerHTML = decodeURI(cookie['login']);
	document.getElementById("exit").innerHTML = decodeURI(cookie['login']);
}
//отправляет форму без перезагрузки страницы, предварительно спрятав ее
function hideAndSend(event, form, param){
	form.style.display='none';
	event.preventDefault();	
	sendForm(form, param);
	//document.getElementById('save').hidden=true;
}
function keydownListener(e){
	var el;
	var flag=false;
	if (e.ctrlKey || e.altKey || e.metaKey) return;	
	if(e.keyCode == 40){
		nextBranch();
		flag=true;
	}else if(e.keyCode == 38){
		prevBranch();
		flag=true;
	}
	if(flag){
		e.stopPropagation();
		e.preventDefault();
	}
}
document.addEventListener('DOMContentLoaded', function() {
	ui_init();
	init();
	initListeners();
	document.addEventListener('keydown', keydownListener);
});


