//отправить форму задач
function taskFormSend(){
	var form = document.forms['active-task'];
	var d,h,m;
	var time;
	updateLastComment();
	d = parseInt(form['days'].value);
	h = parseInt(form['hours'].value);
	m = parseInt(form['minutes'].value);
	if(!isNaN(d)&&!isNaN(h)&&!isNaN(m)){
		time = (d*1440+h*60+m);
		form['plan_time'].value = time;
	}	
	sendForm(form, getFiltersStr());
}