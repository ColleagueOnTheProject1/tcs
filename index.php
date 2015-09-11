<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="css/styles.css" type="text/css">
		<!--набор функций для оптимизации-->
		<script type="text/javascript" src="js/functions.js"></script>
		<script type="text/javascript" src="js/parser.js"></script>
		<script type="text/javascript" src="js/requests.js"></script>
		<script type="text/javascript" src="js/table.js"></script>
		<script type="text/javascript" src="js/ui.js"></script>
		<script type="text/javascript" src="js/manager.js"></script>
		<meta name="viewport" content="width=1400">
		<title>система контроля задач</title>
	</head>
	<body>
		<form class="connect-form modal" id="connect-form" name="connect_form" action="" method="POST" enctype="application/x-www-form-urlencoded">
			<div class="wrapper">
				<div class="c">не удается подключится к серверу, изменить наcтройки?</div>
				сервер<br/>
				<input name="host" placeholder="сервер"/><br/>
				пользователь<br/>
				<input name="user" placeholder="пользователь"/><br/>
				пароль<br/>
				<input name="password" placeholder="пароль"/><br/>
				<button onclick="event.preventDefault();setConfig();">Изменить</button>
				<button>А теперь?</button>
			</div>
		</form>
		<form class="connect-form modal" id="base-form" name="base_form">
			<div class="wrapper">
				<div class="c">база данных не найдена, выбрать другую?</div>
				<button onclick="event.preventDefault();createBase();">Создать её!</button>
				<br/><br/>
				или выбрать базу<br/>
				<input name="base" placeholder="имя базы"/><br/>
				<button onclick="event.preventDefault();setConfig();">Выбрать</button>
			</div>
		</form>
		<form class="connect-form modal" id="login-form" name="login_form" action="" method="post" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault();sendForm(this);">
			<div class="wrapper">
				<div class="c">необходимо войти в систему</div>
				<input name="login" placeholder="имя пользователя"/>
				<input name="password" placeholder="пароль" type=""/>
				<button type="submit">Попробуем</button>
				<div class="hint">не удалось войти!</div>
				<input type="hidden" name="action" value='auth'/>
			</div>
		</form>
		<div id="sureface">
			<div id="img-actions" class="img-actions">
				<img src=""/>
				<div class="">
					<button>заменить</button>
					<button>удалить</button>
					<button onclick="closeImgActions();">назад</button>
				</div>
			</div>
		</div>
		<div id="user" class="user content">
			Здравствуй <span id="login" class="login">Админ</span>!
			<br/>
			<span class="mask">
				<span class="hint">я не <span id="exit" class="exit">Админ</span>, - </span>
				<a class="" href="" onclick="exit();preventDefault();">выйти</a>
			</span>
		</div>
		<div id="tabulator" class="content">
			<input class="tab1" id="tasks-tab" type="radio" name="tab1"/>
			<label for="tasks-tab">Задачи</label>
			<input class="tab1" id="users-tab" type="radio" name="tab1"/>
			<label for="users-tab">Пользователи</label>
			<input class="tab1" id="groups-tab" type="radio" name="tab1" checked="checked"/>
			<label for="groups-tab">Группы</label>
			<div class="page">
				<div id="case-task">
					<h2>Задачи</h2>
					<span id="selected-tasks" class="active-list"></span>
					<br/>
					<button class="btn btn1" onclick="removeSelectedFields();">удалить</button>
					или установить приоритет -
					<button class="btn btn1">низкий</button>
					<button class="btn btn1">средний</button>
					<button class="btn btn1">высокий</button>
					<button class="btn btn1">наивысший</button>
				</div>
				<table id="tasks-table" class="tasks sortable" active_f="activeTask">
					<tr main="title">
						<th name="id" name_f="getDate" to_up="1" class="active">дата создания</th>
						<th name="title" to_up="1">задание</th>
						<th name="priority" name_f="getPriority" to_up="1">приоритет</th>
						<th name="assigned" name_f="getAssigned" to_up="1">назначено на</th>
						<th name="state" name_f="getState" to_up="1">состояние</th>
						<th name="choose" choose_f="chooseRow" choose_list="selected-tasks">Выбрать</th>
					</tr>
				</table>
				<form class="create-task" id="create-task" name="create-task" action="" method="post" enctype="application/x-www-form-urlencoded">
					<button onclick="event.preventDefault();sendForm(getParentForm(this));">Создать задачу</button>
					<input type="hidden" name="action" value="add_task"/>
				</form>
				<form id="active-task" name="active-task" class="detail active-task" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault();sendForm(document.forms['active-task']);">
					<h2>АКТИВНАЯ ЗАДАЧА: </h2><input name="title" type="text" readonly="readonly"/>
					<div id="edit-buttons" class="edit-buttons">
						<img src="../design/edit.jpg" onclick="taskEdit()">
						<img src="../design/apply.jpg" onclick="sendForm(document.forms['active-task']);">
						<img src="../design/cancel.jpg" onclick="activeTask(cur_task);">
					</div>
					<div class="wrapper">
						<div class="inline">
							<div class="">приоритет</div>
							<input type="radio" name="priority" disabled="disabled" value="0"/>
							<input type="radio" name="priority" disabled="disabled" value="1"/>
							<input type="radio" name="priority" disabled="disabled" value="2"/>
							<input type="radio" name="priority" disabled="disabled" value="3"/>
						</div>
						<div class="inline">
							<div class="">Назначено на</div>
							<select name="assigned" disabled="disabled">
								<option>никого</option>
							</select>
						</div>
						<div class="state-buttons inline">
							<input type="hidden" name="state" value="0"/>
							<button type="submit" onclick="getParentForm(this)['state'].value = '1';">начать</button>
							<button type="submit" onclick="getParentForm(this)['state'].value = '2';">приостановить</button>
							<button type="submit" onclick="getParentForm(this)['state'].value = '1';">продолжить</button>
							<button type="submit" onclick="getParentForm(this)['state'].value = '3';">на проверку</button>
							<button type="submit" onclick="getParentForm(this)['state'].value = '2';">переоткрыть</button>
							<button type="submit" onclick="getParentForm(this)['state'].value = '4';">завершить</button>
						</div>
						<div class="inline">
							затрачено времени:<br/><input name="lead_time"/>
						</div>
						<br/><textArea name="text"  readonly="readonly"></textArea>
						<div id="task-images" class="images"></div>
						<input type="hidden" name="images" value=""/>
						<input type="hidden" name="id" value=""/>
						<input type="hidden" name="action" value="save_task"/>

					</div>
				</form>
				<form class="image-form" id="image-form" name="image_form" action="php/action.php" method="POST" enctype="multipart/form-data" onsubmit="event.preventDefault();sendFormData(this);">
					<input type="file" name="uploadfile" accept="image/jpeg,image/png"/>

					<input type="hidden" name="action" value="save_images"/>
					<button type="submit" onclick="">загрузить</button>
				</form>
			</div>

			<div class="users page">
				<h2>Пользователи</h2>
				<span class="">Выбранные пользователи:&nbsp;</span><span id="selected-users"></span><br/>
				<button>Удалить выбранных</button>
				<table id="users-table" class="sortable">
					<tr main="login">
						<th name="type" name_f="getTypeName" class="active" to_up="1">Тип</th>
						<th name="login" to_up="1">Логин</th>
						<th name="password">Пароль</th>
						<th name="tasks" name_f="getTaskCount" to_up="1">задач осталось</th>
						<th name="finished" to_up="1">задач выполнено</th>
						<th name="choose" choose_f="chooseUser">Выбрать</th>
					</tr>
				</table>
				<form class="user-form" id="user-form" name="user-form" action="" method="POST" enctype="application/x-www-form-urlencoded">
				</form>
			</div>
			<div class="groups page">
				<h2>Пользователи</h2>
				<span class="">Выбранные пользователи:&nbsp;</span><span id="selected-users"></span><br/>
				<button>Удалить выбранных</button>
				<table id="groups-table" class="sortable">
					<tr main="login">
						<th name="title" class="active" to_up="1">название</th>
						<th name="choose" choose_f="chooseGroup">Выбрать</th>
					</tr>
				</table>
			</div>

			<div id="footer" class="footer">
			</div>
		</div>
	</body>
</html>
