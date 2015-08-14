/**парсит json объект и вызывает события, соответствующие полю action этого объекта*/

var parser_handlers = [];
function parse(json_data){
	var obj;
	if(!json_data)
		return;
	obj = JSON.parse(json_data);
	if(parser_handlers[obj.action])
		parser_handlers[obj.action]();
}
function showServerForm(){

}