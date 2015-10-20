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
			<input class="tab1" id="tasks-tab" type="radio" name="tab1" checked="checked"/>
			<label for="tasks-tab">Задачи</label>
			<input class="tab1" id="users-tab" type="radio" name="tab1"/>
			<label for="users-tab">Пользователи</label>
			<input class="tab1" id="groups-tab" type="radio" name="tab1" />
			<label for="groups-tab">Группы</label>
			<div class="page">
				<div id="case-task">
					<h2>Задачи</h2>
					<div class="allocate">
						<div id="task-list" class="list1">
						</div>
						<div class="">
							<form id="active-task" name="active-task" class="detail active-task" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault(); sendForm(document.forms['active-task']);">
								<h2>АКТИВНАЯ ЗАДАЧА: </h2><input name="title" type="text" readonly="readonly"/>
								<div id="edit-buttons" class="edit-buttons">
									<img src="../design/edit.jpg" onclick="taskEdit()">
									<img src="../design/apply.jpg" onclick="sendForm(document.forms['active-task']);">
									<img src="../design/cancel.jpg" onclick="activeTask(cur_task);">
								</div>
								<div class="actions">
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
									<div id="task-state-btns" class="state-buttons inline">
										<input type="hidden" name="state" value="0"/>
										<button type="submit" onclick="getParentForm(this)['state'].value = '1';">начать</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '2';">приостановить</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '1';">продолжить</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '3';">на проверку</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '4';">переоткрыть</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '5';">завершить</button>
									</div>
									<div class="inline">
										затрачено времени:<br/><input class="m1" type="text" name="lead_time"/>
									</div>
								</div>
								<div class="wrapper">

									<div class="inline">
										<div id="task-text" class="text"></div>
										<textArea name="text"  readonly="readonly"></textArea>
										<br/><div class="comment"></div>
										<input type="text" class="field1" name="last_comment"/>
										<div id="task-comments" class="text comments"></div>
										<br/><br/><textArea class="no-edit" name="comment"></textArea>
									</div>

									<div id="task-images" class="images"></div>
									<input type="hidden" name="old_state" value="0"/>
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
					</div>
					<span id="selected-tasks" class="active-list"></span>
					<br/>
					<button class="btn btn1" onclick="removeSelectedFields();">удалить</button>
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

			</div>

			<div class="users page">
				<h2>Пользователи</h2>
				<form class="users-remove" id="users-remove" name="users-remove" action="" method="post" enctype="application/x-www-form-urlencoded"  onsubmit="usersRemove(event, this);sendForm(this);">
					<span class="">Выбранные пользователи:&nbsp;</span><span id="selected-users"></span><br/>
					<button type="submit">Удалить выбранных</button>
					<input type="hidden" name="users"/>
					<input type="hidden" name="action" value="remove_users"/>
				</form>
				<table id="users-table" class="sortable" active_f="userActive">
					<tr main="login">
						<th name="type" name_f="getTypeName" class="active" to_up="1">Тип</th>
						<th name="login" to_up="1">Логин</th>
						<th name="password">Пароль</th>
						<th name="tasks" name_f="getTaskCount" to_up="1">задач осталось</th>
						<th name="finished" to_up="1">задач выполнено</th>
						<th name="choose" choose_f="chooseRow" choose_list="selected-users">Выбрать</th>
					</tr>
				</table>
				<form class="user-create" id="user-create" name="user-create" action="" method="post" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault();sendForm(this);">
					<h2>Новый пользователь</h2>
					<input type="text" name="login"/> логин&nbsp;&nbsp;&nbsp;
					<input type="text" name="password"/> пароль
					<button type="submit">Создать</button>
					<input type="hidden" name="action" value="add_user"/>
				</form>
				<form class="user-form" id="user-form" name="user-form" action="" method="POST" enctype="application/x-www-form-urlencoded">
				</form>
				<form class="tasks-completed" id="tasks-completed" name="tasks_completed" action="php/action.php" method="post" enctype="application/x-www-form-urlencoded">
					<h2>Завершенные задачи</h2>
					<table class="sortable">
						<tr main="title">
							<th name="title">название задачи</th>
							<th class="active" to_up="0" name="end_time" name_f="getDate">дата завершения</th>
							<th name="lead_time">затрачено времени</th>
							<th name="choose">выбрать</th>
						</tr>
					</table>
				</form>
			</div>
			<div class="groups page">
				<h2>Группы</h2>
				<span class="">Выбранные группы:&nbsp;</span><span id="selected-groups"></span><br/>
				<button>Удалить выбранных</button>
				<table id="groups-table" class="sortable">
					<tr main="login">
						<th name="title" class="active" to_up="1">название</th>
						<th name="description" to_up="1">описание</th>
						<th name="users" to_up="1" name_f="getCountInList">число участников</th>
						<th name="choose" choose_f="chooseGroup">Выбрать</th>
					</tr>
				</table>
				<form id="group-create" class="group-create create-form" name="group_create" action="" method="post" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault();sendForm(this);">
					<h2>Новая группа</h2>
					<input type="text" name="title"/> название&nbsp;&nbsp;&nbsp;
					<textArea type="text" name="description"/></textArea> описание
					<button type="submit">Создать</button>
					<input type="hidden" name="action" value="add_group"/>
				</form>

			</div>
			<div id="footer" class="footer">
			</div>
		</div>
	</body>
</html>
