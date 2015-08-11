const PRIORITY = ['низкий', "средний", "высокий", "наивысший"];
const SORT_PRIORITY = 2;
//номер колонки активности
const ACTIVITY_COL = 4;
/**table of tasks*/
var tasks;
/**sort fields - th tag array*/
var sortCols;
var sort_id = 0;//id поля сортировки. 0 - по номеру, 1 - по названию, 2 - по приоритету
var sort_way = 1;//упорядочить:0 - по возрастанию; 1 - по убыванию;
var direction = 0;
var data;//тут лежат все задачи(название,описание,приоритет,порядковый номер)
var current;//ссылка на текущий элемент из data
var temp_data;/**используется во время редактирования*/
var curSortField;
var activityField;
var caption;
var edit_fields = [];
var description;
var temp_obj;
var detail;
var imageArr;
var config;
var user_table;
/**number of selected by id*/
var current_id = -1;
document.addEventListener('DOMContentLoaded', function(){
	tasks = document.getElementById("tasks");
	sortCols = tasks.getElementsByTagName("th");	
	detail = document.getElementById("detail");
	edit_fields["priority"] = document.getElementsByName("priority");
	edit_fields["caption"] = document.getElementById("caption");
	edit_fields["description"] = document.getElementById("description");
	images_wrapper = document.getElementById("images");
	document.getElementById("login-form").style.display="block";
	usersInit();
	return;
	cancelEdit();
	update();	
});
