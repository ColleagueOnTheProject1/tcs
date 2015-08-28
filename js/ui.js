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
function showTasks(){
	
}
//обновляет таблицу пользователей
function showUsers(data){
	tableUpdate(document.getElementById('users-table'), data);
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
	console.log(arr);
	document.getElementById('selected-users').innerHTML = arr.join(',');
}