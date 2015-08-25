<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="css/styles.css" type="text/css">
		<!--набор функций для оптимизации-->
		<script type="text/javascript" src="js/functions.js"></script>
		<script type="text/javascript" src="js/parser.js"></script>
		<script type="text/javascript" src="js/requests.js"></script>
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
		<form class="connect-form modal" id="login-form" name="login_form" action="" method="post" enctype="application/x-www-form-urlencoded">
			<div class="wrapper">
				<div class="c">необходимо войти в систему</div>
				<input name="login" placeholder="имя пользователя"/>
				<input name="password" placeholder="пароль" type=""/>
				<button onclick="event.preventDefault();sendForm('login_form');">Попробуем</button>
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
		<div id="tabulator" class="content">
			<input class="tab1" id="tasks-tab" type="radio" name="tab1"/>
			<label for="tasks-tab">Задачи</label>
			<input class="tab1" id="users-tab" type="radio" name="tab1" checked="checked"/>
			<label for="users-tab">Пользователи</label>
			<input class="tab1" id="groups-tab" type="radio" name="tab1" checked="checked"/>
			<label for="groups-tab">Группы</label>
			<div class="users page">
				<h2>Пользователи</h2>
				<button>Удалить выбранных</button>
				<table id="users-table">
					<tr>
						<th name="type" name_f="getTypeName" class="active" to_up="1">Тип</th>
						<th name="login" to_up="1">Логин</th>
						<th name="password">Пароль</th>
						<th name="tasks" name_f="getTaskCount" to_up="1">задач осталось</th>
						<th name="finished" to_up="1">задач выполнено</th>
						<th name="active">Активный</th>
					</tr>
					<tr>
						<td sort="0">Администратор</td>
						<td>user1</td>
						<td>password1</td>
						<td>0</td>
						<td>0</td>
						<td><input name="users-check" type="radio" checked="checked"/></td>
					</tr>
					<tr>
						<td sort="1">Руководитель</td>
						<td>user2</td>
						<td>password2</td>
						<td>0</td>
						<td>3</td>
						<td><input name="users-check" type="radio"/></td>
					</tr>
					<tr>
						<td sort="2">Исполнитель</td>
						<td>user3</td>
						<td>password3</td>
						<td>3</td>
						<td>3</td>
						<td><input name="users-check" type="radio"/></td>
					</tr>
				</table>
				<form class="user-form" id="user-form" name="user-form" action="" method="POST" enctype="application/x-www-form-urlencoded">
				</form>
			</div>
			<div class="page">
				<div id="case-task">
					<h2>операции с выбранными элементами</h2>
					<button class="btn btn1" onclick="removeSelectedFields();">удалить</button>
					или установить приоритет -
					<button class="btn btn1">низкий</button>
					<button class="btn btn1">средний</button>
					<button class="btn btn1">высокий</button>
					<button class="btn btn1">наивысший</button>
				</div>
				<table id="tasks" class="tasks">
					<tr class="sort-field">
						<th onclick="sort(0);">№</th>
						<th onclick="sort(1);">задание</th>
						<th onclick="sort(2);">приоритет</th>
						<th onclick="sort(2);">дата создания</th>
						<th onclick="sort(2);">состояние</th>
						<th>выбор</th>
						<th>активный</th>
					</tr>
				</table>
				<h2>АКТИВНАЯ ЗАДАЧА</h2>
				<form id="detail" class="detail">
					<div id="edit-buttons">
						<img src="../design/edit.jpg" onclick="edit()">
						<img src="../design/apply.jpg" onclick="applyEdit()">
						<img src="../design/cancel.jpg" onclick="cancelEdit()">
					</div>
					<input id="caption" type="text"/>
					<div class="">приоритет</div>
					<input type="radio" name="priority"/>
					<input type="radio" name="priority"/>
					<input type="radio" name="priority"/>
					<input type="radio" name="priority"/>
					<textArea id="description"></textArea>
					<div id="images" class="images">
						<div class="">прикрепленные картинки</div>
					</div>
				</form>
				<form class="image-form" id="image-form" name="image_form" action="" method="POST" enctype="multipart/form-data">
					<input type="file" name="uploadfile" accept="image/jpeg,image/png"/>
					<button onclick="addImage();event.preventDefault();">загрузить</button>
				</form>
			</div>

			<div id="footer" class="footer">
			</div>
		</div>
	</body>
</html>
