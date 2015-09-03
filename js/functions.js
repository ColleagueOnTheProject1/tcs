/**расширенный функции для работы с группой объектов
- повесить событие на массив объектов
*/
/*вешает обработчик события всем элементам из заданного массива*/
function arrAddEventListener(arr, event, callback){
	for (var i = 0; i < arr.length; i++)
		arr[i].addEventListener(event, callback);
}
/**возврашает массив из строки вида cookie*/
function cookieToArr(){
	var arr = {};
	var p;
	var n;	
	var s = document.cookie;
	var i = s.indexOf(';');
	
	while(i != -1){
		p = s.substr(0, i);
		n = p.indexOf("=");
		if(n != -1)
			arr[p.substr(0,n)] = p.substr(n+1);
		s = s.substr(i + 2);
		i = s.indexOf(';');
	}	
	n = s.indexOf("=");
	if(n != -1){
		arr[s.substr(0,n)] = s.substr(n+1);
	}
	return arr;
}
/**сохраняет массив в cookie*/
function setCookie(obj){
	var s = "";
	var key;
	for(key in obj){
		s += key + '=' + obj[key] + '; ';
	}
	console.log(s);
	document.cookie = s;
}
/**добавляет объект в cookie*/
function addCookie(obj){
	var s = document.cookie;
	var key;
	for(key in obj){
		s += key + '=' + obj[key] + '; ';
	}
	document.cookie = s;
}
//удалить куки
function removeCookie(cookie_name){
  var cookie_date = new Date ();  // Текущая дата и время
  cookie_date.setTime (cookie_date.getTime() - 1);
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}