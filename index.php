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
				<table>
					<tr>
						<td>сервер</td>
						<td><input name="host" placeholder="сервер"/></td>
					</tr>
					<tr>
						<td>пользователь</td>
						<td><input name="user" placeholder="пользователь"/></td>
					</tr>
					<tr>
						<td>пароль</td>
						<td><input name="password" placeholder="пароль"/></td>
					</tr>
					<tr>
						<td>база данных</td>
						<td><input name="base" placeholder="база данных"/></td>
					</tr>
				</table>
				cоздать базу при ее отсутствии <input type="checkbox" name="create_base" value="1"/><br/><br/>
				<button type="submit">А теперь?</button>
				<input type="hidden" name="action" value="set_config"/>
			</div>
		</form>
		<form class="connect-form modal" id="login-form" name="login_form" action="" method="post" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault();sendForm(this);this.style.display='none';">
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
		<div id="tasks-info" class="tasks-info">
			<span id="tasks-count" title="осталось задач">10</span>
			<span id="tasks-complete" title="выполнено задач">10</span>
			<span id="tasks-new" title="новых задач">10</span>
			<span id="tasks-reopen" title="переоткрыто задач">10</span>
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
					<form class="filters" id="filters" name="filters" action="" method="post" enctype="application/x-www-form-urlencoded">
						<div class="wrapper">
							<h2>фильтры</h2>
							<div class="inline-top item">пользователи
								<select name="users" onchange="getData(true);">
									<option value="all">Все</option>
								</select>
							</div>
							<div class="inline-top item">группы
								<select name="groups">
									<option>Все</option>
								</select>
							</div>
							<div class="inline-top item">состояние
								<select name="state" onchange="getData(true);">
									<option value="0">не начата</option>
									<option value="1">начата</option>
									<option value="2">приостановлена</option>
									<option value="3">на проверке</option>
									<option value="4">переоткрыта</option>
									<option value="5">завершенные</option>
									<option value="9" selected="selected">не завершенные</option>
								</select>
							</div>

						</div>
					</form>
					<h2>Задачи</h2>
					<div class="allocate">
						<div id="task-list" class="list1"></div>
						<div class="">
							<form id="active-task" name="active-task" class="detail active-task" enctype="application/x-www-form-urlencoded" onsubmit="event.preventDefault(); sendForm(document.forms['active-task'], getFiltersStr());">
								<h2>АКТИВНАЯ ЗАДАЧА: </h2><input class="field2" name="title" type="text" readonly="readonly"/>
								<div id="edit-buttons" class="edit-buttons">
									<img src="../design/edit.jpg" onclick="taskEdit()">
									<img src="../design/apply.jpg" onclick="updateLastComment();sendForm(document.forms['active-task'], getFiltersStr());">
									<img src="../design/cancel.jpg" onclick="activeTask(cur_task);">
									<img src="../design/basket.jpg" onclick="" title="удалить">
								</div>
								<div class="frame1">
									<div class="inline-top">
										<div class="">Назначено на</div>
										<select name="assigned" disabled="disabled">
											<option>никого</option>
										</select>
									</div>
									<div class="inline-top">
										<div class="">тип задачи</div>
										<select name="type" disabled="disabled">
											<option value="0">новинка</option>
											<option value="1">улучшение</option>
											<option value="2">ошибка</option>
										</select>
									</div>
									<div class="inline-top">
										<div class="">группа</div>
										<select name="group" disabled="disabled">
										</select>
									</div>
								</div>
								<div class="frame">
									<div class="inline-top">
										дата создания:<br/><input class="m1" type="text" name="create_date"/>
									</div>&nbsp;
									<div class="inline-top">
										затрачено:<br/><input class="m2" type="text" name="lead_time"/>
									</div>&nbsp;

									<div class="inline-top">
										<div class="">приоритет</div>
										<input type="radio" name="priority" disabled="disabled" value="0"/>
										<input type="radio" name="priority" disabled="disabled" value="1"/>
										<input type="radio" name="priority" disabled="disabled" value="2"/>
										<input type="radio" name="priority" disabled="disabled" value="3"/>
									</div>
								</div>
								<div class="actions frame">

									<div id="task-state-btns" class="state-buttons inline">
										<input type="hidden" name="state" value="0"/>

										<button type="submit" onclick="getParentForm(this)['state'].value = '1';">начать</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '2';">приостановить</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '1';">продолжить</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '3';">на проверку</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '4';">переоткрыть</button>
										<button type="submit" onclick="getParentForm(this)['state'].value = '5';">завершить</button>
									</div>&nbsp;
								</div>
								<div class="wrapper">

									<div class="inline-top">
										<div id="task-text" class="text"></div>
										<textArea name="text"  class="field3" readonly="readonly"></textArea>
										<br/><div class="comment"></div>
										<textArea type="text" class="field4" name="last_comment"></textArea>
										<div id="last-comment" class="text last-comment"></div>
										<div id="task-comment" class="text comments"></div>
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
				</div>
				<form class="create-task" id="create-task" name="create_task" action="" method="post" enctype="application/x-www-form-urlencoded">
					<button onclick="event.preventDefault();sendForm(getParentForm(this), getFiltersStr());">Создать задачу</button>
					<input type="hidden" name="group" value="0"/>
					<input type="hidden" name="assigned" value=""/>
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
				<table id="users-table" class="sortable table" active_f="userActive">
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
					<textArea type="text" name="description"></textArea> описание
					<button type="submit">Создать</button>
					<input type="hidden" name="action" value="add_group"/>
				</form>

			</div>
			<div id="footer" class="footer">
			</div>
		</div>
	</body>
</html>
