/*скрипт распределяет задачи между скриптами. подключается в последнюю очередь*/
//события------------
const ACTION_CONNECT="connect_error";//имя события - не удалось подключится к серверу
const ACTION_BASE="base_error";//имя события - не удалось подключится к базе
const ACTION_GET_TASKS="get_tasks";//имя события - получить данные задачи
const ACTION_GET_USERS="get_users";//имя события - список пользователей с дополнительной информацией
const ACTION_AUTH="auth";//имя события - авторизация
//------------события
var cookie;
function init(){
	parser_handlers[ACTION_CONNECT] = showConnectForm;
	parser_handlers[ACTION_BASE] = showBaseForm;
	parser_handlers[ACTION_GET_TASKS] = showTasks;
	parser_handlers[ACTION_AUTH] = showLoginForm;
	parser_handlers[ACTION_GET_USERS] = showUsers;
	cookie = cookieToArr();
	if(!cookie['login'] || !cookie['password']){
		showLoginForm();
	}else {
		sendAction(ACTION_AUTH);
	}
}
document.addEventListener('DOMContentLoaded', function() {
	init();
});