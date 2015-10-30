/**парсит json объект и вызывает события, соответствующие полю action этого объекта*/

var parser_handlers = [];
function parse(json_data){
	var obj;
	if(!json_data){
		return;
	}
	obj = JSON.parse(json_data);
	
	showLogin();
	if(parser_handlers[obj.action])
		parser_handlers[obj.action](obj.data);
	for(var s in obj){
		if(parser_handlers[s]){
			parser_handlers[ACTION_TASKS_INFO](obj[s]);
		}
	}
}
function showServerForm(){

}