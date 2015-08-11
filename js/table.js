/*Переписывает содержимое таблицы содержимым из data. Ключи берутся из аттрибута name тега th*/
function tableUpdate(data){
	
	
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