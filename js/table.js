/*Переписывает содержимое таблицы содержимым из data. Ключи берутся из аттрибута name тега th*/
function tableUpdate(table, data){
	var row;
	var cell;
	var main_cell;
	var f;
	var name;
	var i;
	if(table.rows.length <= data.length){
		row = table.insertRow();
		for (i = 0; i < table.rows[0].cells.length; i++){
			row.insertCell();
		}
		
	}
	for (var j = 0; j < table.rows[0].cells.length; j++)	
	{
		f = null;
		main_cell = table.rows[0].cells[j];
		name = main_cell.attributes.name.value;
		if(main_cell.attributes['name_f'])
			f = window[main_cell.attributes['name_f'].value];
		for (i = 1; i < table.rows.length; i++)
		{
			cell = table.rows[i].cells[j];
			if(data[i - 1][name]){				
				if(f){
					cell.innerHTML = f(data[i - 1][name]);
					console.log(data[i - 1][name]);
				}
				else{
					cell.innerHTML = data[i - 1][name];
				}
			}
		}
	}
}
/**сортировка таблицы по указанному столбцу. Первый ряд не учавствует в сортировке, - там лежат названия столбцов.*/
function tableSort(table, cell){
	var col = cell.cellIndex;
	//сортировка по значению
	function sort_by_val(row1, row2){
		el1 = row1.cells[col].innetText;
		el2 = row2.cells[col].innerText;

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
	if(table.rows[1].cells[col].attributes.sort.value)
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