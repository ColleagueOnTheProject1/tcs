/*Переписывает содержимое таблицы содержимым из data. 
Ключи берутся из аттрибута name тега th заголовка(первой строки).
Каждая строка имеет аттрибут val содержащий значение главной ячейки.
Главная ячейка определяется по имени аттрибута. 
Если имя аттрибута совпадает со значением аттрибута main заголовка, то она считается главной.
В каждой строке одна главная ячейка td.
в каждую строку пишется аттрибут n, равный индексу в массиве исходных данных для таблицы.
*/
function tableUpdate(table, data){
	var row;
	var cell;
	var cur_cell;
	var f;
	var name;
	var main_attr;
	var i;
	function rowActive(e){
		var table = this;
		while(table.tagName != "TABLE") 
			table = table.parentNode;
		var rows = table.rows;
		for (var i = 1; i < rows.length; i++)
		{
			rows[i].classList.remove('active');
		}
		this.classList.add('active');
		table.rows[0].cells[0].setAttribute('active',this.cells[0].getAttribute('sort'));
		if(table.hasAttribute('active_f')){
			window[table.attributes['active_f'].value](this.attributes['n'].value);
		}
	}
	function cellChoose(e){
		var row = this.parentNode.parentNode.rows[0];
		var temp_cell;
		this.classList.toggle('not');
		if(row.cells[this.cellIndex].hasAttribute('choose_f')){
			temp_cell = row.cells[this.cellIndex];
			window[temp_cell.attributes['choose_f'].value](this.parentNode, temp_cell.attributes['choose_list'].value);
		}
		e.stopPropagation();
	}
	
	if(data.length == 0)
		return;
	while(table.rows.length > 1){
		table.deleteRow(1);
	}
	while (table.rows.length <= data.length){
		row = table.insertRow();
		row.setAttribute('n', row.rowIndex - 1);
		row.addEventListener('click', rowActive);
		for (i = 0; i < table.rows[0].cells.length; i++){
			row.insertCell();
		}
	}
	main_attr = table.rows[0].attributes['main'].value;
	for (var j = 0; j < table.rows[0].cells.length; j++)	
	{
		if(table.rows[0].cells[j].hasAttribute('to_up')){
			table.rows[0].cells[j].addEventListener('click', function(e){
				if(this.classList.contains('active')){
					if(this.attributes['to_up'].value == '1')
						this.setAttribute('to_up','0')
					else
						this.setAttribute('to_up','1');
				}
				var table = this;
				while(table.tagName != "TABLE") 
					table = table.parentNode;
				for(var k = 0; k < table.rows[0].cells.length; k++){
					table.rows[0].cells[k].classList.remove('active');

				}
				table.rows[0].cells[this.cellIndex].classList.toggle('active');
				tableSort(table,this);
			});
		}
		f = null;
		cur_cell = table.rows[0].cells[j];
		name = cur_cell.attributes.name.value;
		if(cur_cell.hasAttribute('name_f'))
			f = window[cur_cell.attributes['name_f'].value];
		for (i = 1; i < table.rows.length; i++)
		{
			cell = table.rows[i].cells[j];
			if(data[i - 1][name] != undefined){
				if(f){
					cell.innerHTML = f(data[i - 1][name]);
					cell.setAttribute('sort', data[i - 1][name]);
				}
				else{
					cell.innerHTML = data[i - 1][name];
				}
				if(name == main_attr){
					table.rows[i].setAttribute('val', cell.innerHTML);
				}
			}else if(name == 'choose'){
				cell.classList.add('choose');
				cell.classList.add('not');
				cell.addEventListener('click',cellChoose);
			}
		}
	}
	for(i = 1; i < table.rows.length; i++){
		if(table.rows[i].cells[0].getAttribute('sort') == table.rows[0].cells[0].getAttribute('active')){
			table.rows[i].click();
			break;
		}
	}
	if(i == table.rows.length)
		table.rows[1].click();
}
/**сортировка таблицы по указанному столбцу. Первый ряд не учавствует в сортировке, - там лежат названия столбцов.*/
function tableSort(table, cell){
	var col;
	var i;
	if (!cell)	{
		for(i = 0; i < table.rows[0].cells.length; i++){
			if(table.rows[0].cells[i].classList.contains('active')){
				cell = table.rows[0].cells[i];
				break;
			}
		}
	}	
	col = cell.cellIndex;
	//сортировка по значению
	function sort_by_val(row1, row2){
		el1 = row1.cells[col].innerHTML;
		el2 = row2.cells[col].innerHTML;
		if((to_up && el1 > el2)||(!to_up && el2 > el1)){
			table.rows[i].parentNode.insertBefore(row2,row1);
		}
	}
	//сортировка по аттрибуту sort
	function sort_by_attr(row1, row2){
		el1 = row1.cells[col].attributes.sort.value;
		el2 = row2.cells[col].attributes.sort.value;
		if((to_up && el1 > el2)||(!to_up && el2 > el1)){
			table.rows[i].parentNode.insertBefore(row2,row1);
		}
	}
	var el1, el2;
	var sort_f;
	var to_up = true;
	if(table.rows[0].cells[col].attributes.to_up.value == "0")
		to_up = false;
	if(table.rows[1].cells[col].attributes.sort)
		sort_f = sort_by_attr
	else
		sort_f = sort_by_val;
	for(i = 1; i < table.rows.length - 1; i++){
		for(var j = i + 1; j < table.rows.length; j++){
			sort_f(table.rows[i], table.rows[j]);
		}
	}
}
//врзвращает псевдозначение параметра, из таблицы задач для заданного имени параметра по id задачи
function getTaskData(id, field_name){
	var val;
	switch(field_name){
		case 'tasks':val = getTaskCount(tasks[id][field_name]);break;
		default:val = tasks[id][field_name];break;
	}
	return val;
}
//возвращает название типа пользователя по его типу
function getTypeName(type){
	var types = ['Админ', 'Руководитель', 'Исполнитель'];
	return types[type];
}
//возвращает количество задачи из строки, в которой они расположены через запятую
function getTaskCount(tasks){
	var k = 1;
	if (!tasks.length)	{
		return 0;
	}
	for (var i = 0; i < tasks.length; i++)	{
		if(tasks[i] == ','){
			k++;
		}
	}
	return k;
}
//сохраняем необходимые данные по задачам, пользователям и группам.
function getInfo(data){
	info = data;
	sendAction(ACTION_AUTH);
}
//возвращает состояние задания по его id
function getState(id){
	var states = ['не начата','начата', 'приостановлена','на проверке','переоткрыта', 'приостановлена'];
	return states[id];
}
//
function getDate(date){
	var d = new Date(parseInt(date)*1000);
	return d.getDate() + '.' + (d.getMonth() + 1) + '.' + d.getFullYear() + ' в ' + d.getHours() + ':' + Math.floor(d.getMinutes()/10) +  (d.getMinutes() % 10);
}
function getPriority(id){
	const arr = ['низкий', 'средний', 'высокий', 'наивысший'];
	return arr[id]; 
}
//переписывает пустой логин на значение по умолчанию или возвращает тот же логин.
function getAssigned(s){
	if(!s)
		return 'Пушкин, что-ли?'
	else
		return s;
}
//настройка всех таблиц
function tablesInit(){
	var tables = document.getElementsByClassName('sortable');
	var sort_cells;
	var cell;
	for (var i = 0; i < tables.length; i++)
	{
		for (j = 0; j < tables[i].rows[0].cells; j++)
		{
			cell = tables[i].rows[0].cells[j];
			if(cell.hasAttribute('to_up')){
				cell.addEventListener('click', function(e){
					tableSort(this.parent.parent.parent, this);
				});
			}
		}
	}
}