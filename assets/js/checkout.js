jQuery(document).ajaxComplete(function(){
	jQuery("#wcj_input_field_bank_from").replaceWith('<select id="wcj_input_field_bank_from" name="wcj_input_field_bank_from">' +
		'<option value="" selected>- حدد -</option>' +
		'<option value="البنك الأهلي التجاري">البنك الأهلي التجاري</option>' +
		'<option value="البنك السعودي للإستثمار">البنك السعودي للإستثمار</option>' +
		'<option value="سامبا">سامبا</option>' +
		'<option value="مصرف الأنماء">مصرف الأنماء</option>' +
		'<option value="مصرف الراجحي">مصرف الراجحي</option>' +
		'<option value="البنك العربي الوطني">البنك العربي الوطني</option>' +
		'<option value="بنك الرياض">بنك الرياض</option>' +
		'<option value="ساب">ساب</option>' +
		'<option value="البنك الأول">البنك الأول</option>' +
		'<option value="البنك السعودي الفرنسي">البنك السعودي الفرنسي</option>' +
		'<option value="ميم ">ميم </option>' +
		'<option value="بنك البلاد">بنك البلاد</option>' +
		'<option value="البنك العربي الوطني">البنك العربي الوطني</option>' +
		'<option value="بنك الجزيرة">بنك الجزيرة</option>' +
        '</select>');
        
        jQuery("#wcj_input_field_bank_to").replaceWith('<select id="wcj_input_field_bank_to" name="wcj_input_field_bank_to">' +
		'<option value="" selected>- حدد -</option>' +
		'<option value="البنك الأهلي التجاري">البنك الأهلي التجاري</option>' +
		'<option value="البنك السعودي للإستثمار">البنك السعودي للإستثمار</option>' +
		'<option value="سامبا">سامبا</option>' +
		'<option value="مصرف الأنماء">مصرف الأنماء</option>' +
		'<option value="ساب">ساب</option>' +
        '</select>');
});