/**парсит json объект и вызывает события, соответствующие полю action этого объекта*/

var parser_handlers;
function parse(json_data){
	var obj = JSON.parse(json_data);
	parser_handlers[obj.action]();
}
function showServerForm(){

}