/**парсит json объект и вызывает события, соответствующие полю action этого объекта*/

var parser_handlers = [];
function parse(json_data){
	var obj;
	if(!json_data){
		return;
	}
	obj = JSON.parse(json_data);
	
	showLogin();
	for(var s in obj){
		if(parser_handlers[s]){
			parser_handlers[s](obj[s]);
		}
	}
	if(parser_handlers[obj.action] && obj.data){
		parser_handlers[obj.action](obj.data);
	}
}
function showServerForm(){

}