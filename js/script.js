/**куча функций не связанных с архитектурой приложения
основной скрипт содержит глобальные переменные и распоряжается запуском необходимых функций,
а этот скрипт содержит описание данных функций
*/
/*делает задание активным*/
function taskActivate(e){
	for (var i = 0; i < activityField.length; i++){
		activityField[i].checked = false;
	}	
	e.target.checked = true;
	i = 0;
	while(i < activityField.length && e.target != activityField[i])
		i++;
	current_id = data[i]["id"];
	edit_fields["description"].value = data[i]["description"];
	edit_fields["caption"].value = data[i]["caption"];
	edit_fields["priority"][data[i]["priority"]].checked = true;
	var temp_arr = data[i]["images"].split(';');
	temp_arr.length = temp_arr.length - 1;//удалил пустой элеммент, так как последний элемент, кончается разделителем
	var old_images = images_wrapper.getElementsByTagName("img");
	var j;
	for (j = old_images.length - 1; j >= 0; j--){
		images_wrapper.removeChild(old_images[j]);
	}
	for (j  = 0; j < temp_arr.length; j++)
	{
		images_wrapper.innerHTML += '<img src="images/' + temp_arr[j] + '"/>';
	}
	imageArr = images_wrapper.getElementsByTagName("img");
	addActionsForImages();

	temp_data = data[i];
	return false;
}
/**сортирует текущие данные по выбранной колонке(id)
id = 0..3;
*/
function sort(id){
	var temp;
	if (id != sort_id){
		sort_way = 1;
	}else
		sort_way = (sort_way + 1) % 2;
	sort_id = id;
	for (var i = 0; i < sortCols.length; i++){
		sortCols[i].classList.remove("down", "up");
	}
	if(sort_way == 1)
		sortCols[id].classList.add("up");
	else
		sortCols[id].classList.add("down");
	switch(sort_id){
		case 0:field = "id";
			break;
		case 1:field = "caption"
			break;
		case 2:field = "priority"
			break;
	}
	for(var i = 0; i < data.length - 1; i++)
		for(var j = i + 1; j < data.length; j++){
			if(sort_way == 0){
				if(data[i][field] > data[j][field]){
					temp = data[i];
					data[i] = data[j];
					data[j] = temp;
				}
			}
			else{ 
				if(data[i][field] < data[j][field]){
					temp = data[i];
					data[i] = data[j];
					data[j] = temp;
				}
			}
		}
	redraw();
}

/**обновляет текущие данные на экране*/
function redraw(){
		while(tasks.rows.length - 1 > data.length)
			tasks.deleteRow(2);
		if(data.length)
		for (var i = tasks.rows.length - 1; i < data.length; i++){
			tasks.insertRow();
			for (j = 0; j < tasks.rows[0].cells.length; j++){
				tasks.rows[i + 1].insertCell();
			}
		}
		activityField = [];
		for (var i = 0; i < data.length; i++){
			tasks.rows[i + 1].cells[0].innerHTML = data[i].num;
			tasks.rows[i + 1].cells[1].innerHTML = data[i].caption;
			tasks.rows[i + 1].cells[2].innerHTML = data[i].priority;
			tasks.rows[i + 1].cells[3].innerHTML = "<input type='checkbox'>";
			tasks.rows[i + 1].cells[4].innerHTML = "<input type='checkbox'>";
			activityField.push(tasks.rows[i + 1].cells[ACTIVITY_COL].getElementsByTagName("input")[0]);
			activityField[i].addEventListener("click", taskActivate);
		}		
		taskActivate({"target": activityField[0]});
}

/**отображает поля для редактирования заголовка, описания*/
function edit(e){
	detail.classList.add("edit");
	var temp_obj = detail.getElementsByTagName("input");
	setEditFieldsDisabled(false);
}
/**disabled:true,false*/
function setEditFieldsDisabled(disabled){
	edit_fields["caption"].disabled = disabled;
	edit_fields["description"].disabled = disabled;
	for (var i = 0; i < edit_fields["priority"].length; i++){
		edit_fields["priority"][i].disabled = disabled;
	}
}
function cancelEdit(e){
	detail.classList.remove("edit");
	setEditFieldsDisabled(true);
}
function applyEdit(e){
	detail.classList.remove("edit");	
	temp_data["caption"] = edit_fields["caption"].value;
	temp_data["description"] = edit_fields["description"].value;
	editTaks();
}
/**открывает меню для картинки:обновить, удалить, закрыть меню*/
function openImgActions(e){
	document.getElementById("img-actions").getElementsByTagName("img")[0].src = e.target.src;
	document.getElementById("img-actions").style.display = "table-cell";
	document.getElementById("sureface").style.display = "table";
}
/**закрывает окно действий для картинки*/
function closeImgActions(){
	document.getElementById("img-actions").style.display = "none";
	document.getElementById("sureface").style.display = "none";
}
/**закрывает окно действий для картинки*/
function addActionsForImages(){
	for (var i = 0; i < imageArr.length; i++)
	{
		imageArr[i].addEventListener("click", openImgActions);
	}
}
/**добавляет картинку к текущей задаче*/
function addImage(){
	// создать объект для формы
	var formData = new FormData(document.forms.image_form);
	// отослать
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "save_image.php", false);
	xhr.send(formData);
	images_wrapper.innerHTML += '<img src="design/' + xhr.responseText + '"/>';
}
/***/
function usersInit(){
	user_table = document.getElementById("users-table");
	var cell;
	for(var i = 0; i < user_table.rows[0].cells.length; i++){
		cell = user_table.rows[0].cells[i];
			if(cell.attributes.to_up){
				cell.addEventListener("click", function(){
					user_table.getElementsByClassName("active")[0].classList.remove("active");
					this.classList.add("active");
					if(this.attributes.to_up.value == "0")
						this.attributes.to_up.value = "1"
					else
						this.attributes.to_up.value = "0";
					tableSort(user_table, this);
				})
			}
	}
	tableSort(user_table, user_table.rows[0].cells[0]);
}
/***/

