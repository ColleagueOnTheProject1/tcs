/**расширенный функции для работы с группой объектов
- повесить событие на массив объектов
*/
/*вешает обработчик события всем элементам из заданного массива*/
function arrAddEventListener(arr, event, callback){
	for (var i = 0; i < arr.length; i++)
		arr[i].addEventListener(event, callback);
}