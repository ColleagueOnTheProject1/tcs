/*Переписывает содержимое таблицы содержимым из data. 
Ключи берутся из аттрибута name тега th заголовка(первой строки).
Каждая строка имеет аттрибут val содержащий значение главной ячейки.
Главная ячейка определяется по имени аттрибута. 
Если имя аттрибута совпадает со значением аттрибута main заголовка, то она считается главной.
В каждой строке одна главная ячейка td.
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
		var rows = this.parentNode.parentNode.rows;
		for (var i = 1; i < rows.length; i++)
		{
			rows[i].classList.remove('active');
		}
		this.classList.toggle('active');
	}
	function cellChoose(e){
		var row = this.parentNode.parentNode.rows[0];
		this.classList.toggle('not');
		if(row.cells[this.cellIndex].hasAttribute('choose_f')){
			window[row.cells[this.cellIndex].attributes['choose_f'].value](this.parentNode);
		}
		e.stopPropagation();
	}
	while(table.rows.length > 1){
		table.deleteRow(2);
	}
	while (table.rows.length <= data.length){
		row = table.insertRow();
		row.addEventListener('click', rowActive);
		for (i = 0; i < table.rows[0].cells.length; i++){
			row.insertCell();
		}
	}
	table.rows[1].classList.add('active');
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
}
/**сортировка таблицы по указанному столбцу. Первый ряд не учавствует в сортировке, - там лежат названия столбцов.*/
function tableSort(table, cell){
	var col = cell.cellIndex;
	//сортировка по значению
	function sort_by_val(row1, row2){
		el1 = row1.cells[col].innerHTML;
		el2 = row2.cells[col].innerHTML;
		console.log(to_up, el1, el2);
		if((to_up && el1 > el2)||(!to_up && el2 > el1)){
			table.rows[i].parentNode.insertBefore(row2,row1);
			console.log('sort_by_val');
		}
	}
	//сортировка по аттрибуту sort
	function sort_by_attr(row1, row2){
		el1 = row1.cells[col].attributes.sort.value;
		el2 = row2.cells[col].attributes.sort.value;
		if((to_up && el1 > el2)||(!to_up && el2 > el1)){
			table.rows[i].parentNode.insertBefore(row2,row1);
			console.log('sort_by_attr');
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
	for(var i = 1; i < table.rows.length - 1; i++){
		for(var j = i + 1; j < table.rows.length; j++){
			sort_f(table.rows[i], table.rows[j]);
		}
	}
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
//возвращает состояние задания по его id
function getState(id){
	var states = ['не назначена','назначена','начата', 'приостановлена','закрыта','переоткрыта'];
	return states[id];
}
//
function getDate(date){
	return new Date(date).toLocaleString();
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