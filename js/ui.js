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
	form['lead_time'].value = tasks[taskId]['lead_time'];
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
}
/*берет логин пользователя из строки таблицы и добавляет его в список/строку выбранных. 
Если пользователь уже есть - удаляет его из списка*/
function chooseUser(row){
	var login =  row.attributes['val'].value;
	var s = document.getElementById('selected-users').innerHTML;
	var arr = s.split(',');
	var i = 0;
	if(arr[0] == ''){
		arr = [];
	}
	while(i < arr.length && arr[i] != login){
		i++;
	}
	if(i < arr.length){
		arr.splice(i,1);
	}else{
		arr.push(login); 
	}
	document.getElementById('selected-users').innerHTML = arr.join(',');
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
		arr.push(val); 
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