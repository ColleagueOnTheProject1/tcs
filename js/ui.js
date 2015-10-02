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
//обновляет таблицу задач
function showTasks(data){
	tasks = data;
	tableUpdate(document.getElementById('tasks-table'), data);	
	tableSort(document.getElementById('tasks-table'));
	taskListUpdate(data);
}
/*обновить список задач в каталоге*/
function taskListUpdate(data){
	var taskList = document.getElementById('task-list');
	var li;
	var ch;
	var sub;
	var subs = [];
	function createItem(index){
		ch = document.createElement('input');
		ch.setAttribute('type', 'radio');
		ch.setAttribute('name', 'task-group');
		ch.setAttribute('id', 't-l-line-id'+index);
		ch.checked = false;		
		li = document.createElement('label');
		li.setAttribute('for', 't-l-line-id'+index);
		li.setAttribute('n',index);
		li.innerHTML = data[index]['title'];	
		li.addEventListener('click', function(e){activeTask(this.getAttribute('n'))});
	}
	taskList.innerHTML = "";
	for(var i = 0; i < data.length; i++){	
		if(data[i]['owner_task'] == 0){//добавляем задачу
			createItem(i);
			taskList.appendChild(ch);
			taskList.appendChild(li);
		}
	}
	for(var i = 0; i < data.length; i++){
		if(data[i]['owner_task'] > 0){//добавляем подзадачу
			createItem(i);
			for(var j = 0; j < subs.length; j++){
				if(subs[j].getAttribute('owner') == data[i]['owner_task'])
					break;
			}			
			if(j == subs.length){
				sub = document.createElement('div');
				sub.classList.add('sub');
				sub.setAttribute('owner', data[i]['owner_task']);
				subs.push(sub);
				taskList.appendChild(sub);
			}else{
				sub = subs[j];
			}
			sub.appendChild(ch);
			sub.appendChild(li);
		}
	}
}
//активировать пользователя для просмотра информации о нем
function userActive(userId){

}
//активировать задачу для просмотра
function activeTask(taskId){
	var form = document.getElementById('active-task');
	var imgs = [];
	var task_images = document.getElementById('task-images');
	cur_task = taskId;
	form['title'].value = tasks[taskId]['title'];
	form['text'].value = tasks[taskId]['text'];

	form['priority'][tasks[taskId]['priority']].checked = true;
	form['images'].value = tasks[taskId]['images'];
	form['id'].value = tasks[taskId]['id'];
	form['state'].value = tasks[taskId]['state'];
	form['old_state'].value = tasks[taskId]['state'];
	form['lead_time'].value = tasks[taskId]['lead_time'];
	form['last_comment'].value = '';
	form['comment'].value = tasks[taskId]['comment'];

	document.getElementById('task-text').innerHTML = form['text'].value;
	document.getElementById('task-comments').innerHTML = form['comment'].value;

	var logins;
	logins = info['users'].split(',');
	for(var i = 0; i < logins.length; i++){
		form['assigned'].options[i + 1] = new Option(logins[i]);
		if(tasks[taskId]['assigned'] == logins[i]){
			form['assigned'].options[i + 1].selected = true;
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
//обновляет таблицу пользователей
function showUsers(data){
	users = data;
	tableUpdate(document.getElementById('users-table'), data);
	tableSort(document.getElementById('users-table'));
	sendAction(ACTION_GET_COMPLETE_TASKS);
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
	var form = document.getElementById('active-task');
	form.classList.add('edit');
	form['title'].readOnly = false;
	form['text'].readOnly = false;
	form['assigned'].disabled = false;
	document.getElementById('task-state-btns').classList.add('hidden');
	for(var i =0; i < form['priority'].length; i++){
		form['priority'][i].disabled = false;
	}
	document.forms['image-form'].style.display = 'block';
}

function taskCancelEdit(){
	var form = document.getElementById('active-task');
	form.classList.remove('edit');
	form['title'].readOnly = true;
	form['text'].readOnly = true;		
	form['assigned'].disabled = true;
	document.getElementById('task-state-btns').classList.remove('hidden');
	for(var i =0; i < form['priority'].length; i++){
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