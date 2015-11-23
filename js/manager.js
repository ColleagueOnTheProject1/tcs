/*скрипт распределяет задачи между скриптами. подключается в последнюю очередь*/
const STATES = ['не начата','начата', 'приостановлена','на проверке', 'переоткрыта','закрыта'];
const PRIORITIES = ['низкий', 'средний', 'высокий', 'наивысший'];
//события------------
const ACTION_CONNECT="connect";//имя события - подключение к серверу
const ACTION_BASE="base_error";//имя события - не удалось подключится к базе
const ACTION_GET_TASKS="get_tasks";//имя события - получить данные задачи
const ACTION_GET_COMPLETE_TASKS="get_complete_tasks";//имя события - получить список завершенных задач
const ACTION_GET_USERS="get_users";//имя события - список пользователей с дополнительной информацией
const ACTION_GET_GROUPS="get_groups";//имя события - группы
const ACTION_AUTH="auth";//имя события - авторизация
const ACTION_TASK_IMAGE="task_image";//имя события - картинка сохранена на сервер
const ACTION_GET_INFO="get_info";//имя события - получить id групп, задач и пользователей
const ACTION_ADD_USER="add_user";//имя события - добавить пользователя
const ACTION_ADD_GROUP="add_group";//имя события - добавить группу
const ACTION_REMOVE_USER="remove_user";//имя события - удалить пользователей
const ACTION_TASKS_INFO="tasks_info";//имя события - пришла информация о задачах
const ACTION_U_TASK_COUNT="u_task_count";//имя события - пришла информация о количестве задач текущего пользователя


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
var info;
var filter = false;
function init(){	
	parser_handlers[ACTION_CONNECT] = showConnectForm;
	parser_handlers[ACTION_BASE] = showBaseForm;
	parser_handlers[ACTION_GET_TASKS] = showTasks;
	parser_handlers[ACTION_GET_GROUPS] = showGroups;
	parser_handlers[ACTION_AUTH] = showLoginForm;
	parser_handlers[ACTION_GET_USERS] = showUsers;
	parser_handlers[ACTION_TASK_IMAGE] = taskAddImage;
	parser_handlers[ACTION_GET_INFO] = getInfo;
	parser_handlers[ACTION_U_TASK_COUNT] = tasksInfoUpdate;	
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
	document.getElementById("exit").innerHTML = cookie['login'];
}
document.addEventListener('DOMContentLoaded', function() {
	init();
	initListeners();
});