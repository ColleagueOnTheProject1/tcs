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
		document.getElementById('login-form').login.value = cookie['login'];
		if(cookie['password'])
			document.getElementById('login-form').password.value = cookie['password'];
		document.getElementById('login-form').getElementsByClassName("hint")[0].classList.add('animate');
	}else{
		document.getElementById('login-form').getElementsByClassName("hint")[0].classList.remove('animate');
	}
	document.getElementById('login-form').style.display = "block";	
}
//обновляет таблицу групп
function showGroups(data){
	groups = data;
	tableUpdate(document.getElementById('groups-table'), data);	
	tableSort(document.getElementById('groups-table'));
}
//обновляет таблицу задач и статус
function showTasks(data){
	if(!tasks)
		task_status = 1
	else if(data.length == tasks.length)
		task_status = 2
	else if(data.length > tasks.length)
		task_status = 3
	else
		task_status = 4//удалена
	tasks = data;
	taskListUpdate(data);
}
/*обновить список задач в каталоге*/
function taskListUpdate(data){
	var taskList = document.getElementById('task-list');
	var i;
	var el,el2;
	var gr;//группа
	var gt_arr = [];//список заголовков групп
	var g_arr = [];//массив групп
	var t_arr = [];//массив задач
	var og;//основная группа
	function getTaskNum(task_id){
		for(var i = 0; i < tasks.length; i++){
			if(tasks[i]['id'] == task_id)
				break;
		}
		return i;
	}
	function selGroup(e){
		for(i = 0; i < gt_arr.length; i++){
			gt_arr[i].classList.remove('active');
		}
		e.target.classList.add('active');
	}
	function selTask(e){
		var n;
		var p;
		p = e.target.parentNode;
		for(i = 0; i < t_arr.length; i++){
			t_arr[i].classList.remove('active');
		}
		for(i = 0; i < g_arr.length; i++){
			if(p == g_arr[i])
				break;
		}
		e.target.classList.add('active');
		n = e.target.getAttribute('n');
		last_task_id = data[n]['id'];
		activeTask(n);
		selGroup({target:gt_arr[i]});
		document.forms['create_task']['group'].value = tasks[n]['group'];
	}
	taskList.innerHTML = "";
	if(!data.length)
		return;
	for(i = 0; i < info['group_ids'].length; i++){//добавляем группы
		el = document.createElement('div');		
		el.classList.add('c');
		el.innerHTML = info['group_titles'][i];
		el.click(selGroup);
		gt_arr.push(el);
		taskList.appendChild(el);
		el = document.createElement('div');
		el.setAttribute('id', 'group_id_'+info['group_ids'][i]);
		el.classList.add('sub');
		g_arr.push(el);
		taskList.appendChild(el);
	}
	og = document.getElementById('group_id_0');
	for(i = 0; i < data.length; i++){//добавляем задачи
		el = document.createElement('div');
		el.classList.add('t');
		el.setAttribute('n', i);
		el.setAttribute('state', data[i]['state']);
		el.setAttribute('priority', data[i]['priority']);
		el.innerHTML = data[i]['title'];
		el.addEventListener('click', selTask);
		t_arr.push(el);
		el2 = document.getElementById('group_id_' + data[i]['group']);
		if(!el2){
			el2 = og;
		}
		el2.appendChild(el);	
	}
	if(!last_task_id || task_status == 4){
		i = 0;
	}else if(task_status == 3){
		i = tasks.length - 1;
	}else
		i = getTaskNum(last_task_id);
	t_arr[i].click();
	filter = false;
}
//активировать пользователя для просмотра информации о нем
function userActive(userId){

}
//активировать задачу для просмотра. taskId - номер задачи в массиве задач.
function activeTask(taskId){
	var form = document.getElementById('active-task');
	var imgs = [];
	var task_images = document.getElementById('task-images');
	if(tasks[taskId]['state']==5){
		document.getElementById('edit-buttons').classList.add('closed');
	}else
	{
		document.getElementById('edit-buttons').classList.remove('closed');
	}
	cur_task = taskId;
	form['title'].value = tasks[taskId]['title'];
	form['text'].value = tasks[taskId]['text'];

	form['priority'][tasks[taskId]['priority']].checked = true;
	form['images'].value = tasks[taskId]['images'];
	form['id'].value = tasks[taskId]['id'];
	form['state'].value = tasks[taskId]['state'];
	form['old_state'].value = tasks[taskId]['state'];
	form['lead_time'].value = tasks[taskId]['lead_time'] + ' сек';
	form['create_date'].value = getDate(tasks[taskId]['id']);
	form['last_comment'].value = '';
	form['comment'].value = tasks[taskId]['comment'];
	form['type'].value = tasks[taskId]['type'];
	document.getElementById('task-text').innerHTML = form['text'].value;
	document.getElementById('task-comment').innerHTML = tasks[taskId]['comment'];
	document.getElementById('last-comment').innerHTML = tasks[taskId]['last_comment'];
	
//	document.getElementById('tasks-count').innerHTML = tasks[taskId]['tasks_count'];
	var logins;
	logins = info['users'].split(',');
	for(var i = 0; i < logins.length; i++){
		form['assigned'].options[i + 1] = new Option(logins[i]);
		if(tasks[taskId]['assigned'] == logins[i]){
			form['assigned'].options[i + 1].selected = true;
		}
	}
	for(i = 0; i < info['group_ids'].length;i++){
		if(info['group_ids'][i] == tasks[taskId]['group']){
			form['group'].options[i].selected = true;
		}else{
			form['group'].options[i].selected = false;
		}
	}
	
	if(tasks[taskId]['images'])
		imgs = tasks[taskId]['images'].split(',');
	task_images.innerHTML = "";
	for(i = 0; i < imgs.length; i++){
		task_images.innerHTML+='<img src="images/'+ imgs[i] + '" alt=""/>';
	}
	taskCancelEdit();
}
//обновляем список пользователей 
function updateUsersList(data){
	var u_filters = document.forms['filters']['users'];
	if(!data.length)
		return;
	info['users'] = data[0]['login'];
	u_filters.innerHTML = '<option value="all">Все</option>';
	for(var i = 1; i < data.length; i++){
		info['users'] += ',' + data[i]['login'];
		opt = document.createElement('option');
		opt.value = data[i]['login'];
		u_filters.appendChild(opt);
	}	
}
//обновляет списки фильтров
function updateFilters(){
	var u_filters = document.forms['filters']['users'];
	var g_filters = document.forms['filters']['groups'];
	var s_filters = document.forms['filters']['state'];
	var t_g = document.forms['active-task']['group'];
	var opt;
	var arr;
	var old_v;
	arr = info['users'].split(',');
	old_v = u_filters.value;
	u_filters.innerHTML = '<option value="all">Все</option>';
	for(var i = 0; i < arr.length; i++){
		opt = document.createElement('option');
		opt.value = arr[i];
		opt.innerHTML = arr[i];
		if(opt.value == old_v){
			opt.selected = true;
		}
		u_filters.appendChild(opt);
	}	
	arr = info['group_titles'];
	old_v = g_filters.value;
	g_filters.innerHTML = '<option value="all">Все</option>';
	t_g.innerHTML = '';
	for(i = 0; i < arr.length; i++){
		opt = document.createElement('option');
		opt.value = arr[i];
		opt.innerHTML = arr[i];
		if(opt.value == old_v){
			opt.selected = true;
		}
		g_filters.appendChild(opt);
		opt = document.createElement('option');
		opt.value = info['group_ids'][i];
		opt.innerHTML = arr[i];
		t_g.appendChild(opt);
	}	
}
//возвращает строку с фильтрами для get запроса
function getFiltersStr(){
	var s;
	s = 'u_filter=' + document.forms['filters']['users'].value + '&g_filter=' + 
		document.forms['filters']['groups'].value + '&s_filter=' + document.forms['filters']['state'].value;
	return s;
}
//обновляет таблицу пользователей
function showUsers(data){
	users = data;
	tableUpdate(document.getElementById('users-table'), data);
	tableSort(document.getElementById('users-table'));
	updateUsersList(data);
	sendAction(ACTION_GET_COMPLETE_TASKS);
	document.getElementById('selected-users').innerHTML = '';
}
/*
Обновляет список выбора.
Берет значение главной ячейки из строки таблицы и добавляет его в список/строку выбранных. 
Если значение уже есть - удаляет его из списка*/
function chooseRow(row, list){
	var val =  row.attributes['val'].value;
	var s = document.getElementById(list).innerHTML;
	var arr = s.split(', ');
	var i = 0;
	if(arr[0] == ''){
		arr = [];
	}
	while(i < arr.length && arr[i] != val){
		i++;
	}
	if(i < arr.length){
		arr.splice(i,1);
	}else{
		arr.push("'"+val+"'"); 
	}
	document.getElementById(list).innerHTML = arr.join(', ');
}

//открывает редактирование задачи
function taskEdit(){
	var i;
	var form = document.getElementById('active-task');
	form.classList.add('edit');
	form['title'].readOnly = false;
	form['text'].readOnly = false;
	selects = form.getElementsByTagName('select');
	for(i = 0; i< selects.length; i++){
		selects[i].disabled = false;
	}
	document.getElementById('task-state-btns').classList.add('hidden');
	for(i =0; i < form['priority'].length; i++){
		form['priority'][i].disabled = false;
	}
	document.forms['image-form'].style.display = 'block';
}

function taskCancelEdit(){
	var form = document.getElementById('active-task');
	var selects;
	var i;
	form.classList.remove('edit');
	form['title'].readOnly = true;
	form['text'].readOnly = true;		
	selects = form.getElementsByTagName('select');
	for(i = 0; i< selects.length; i++){
		selects[i].disabled = true;
	}
	document.getElementById('task-state-btns').classList.remove('hidden');
	for(i =0; i < form['priority'].length; i++){
		form['priority'][i].disabled = true;
	}
	document.forms['image-form'].style.display = 'none';
}
function taskAddImage(data){
	var form = document.forms['active-task'];
	document.getElementById('task-images').innerHTML += '<img src="images/' + data + '" alt=""/>';
	if(form['images'].value != '')
		form['images'].value += ',';
	form['images'].value += data;
}
//удалить выбранных пользователей
function usersRemove(event, form){
	event.preventDefault();
	form['users'].value = document.getElementById('selected-users').innerHTML;
}
//добавить значение поля в первую строку другого поля
function addToField(field, value){
	if (value)
	{
		field.value = '\n' + value;
	}
}
//обновить список завершенных задач
function updateCompleteTasks(data){
	var table = document.getElementById('tasks-completed').getElementsByTagName('table')[0];
	tableUpdate(table, data);
}
//добавить комментарий в список последних комментариев
function updateLastComment(){
	var form = document.getElementById('active-task');
}