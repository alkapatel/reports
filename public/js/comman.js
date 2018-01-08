$(document).ready(function(){
    $('.form').parsley();
    $('.form2').parsley();
    $('.form3').parsley();
});

    
/* tiny mce configration */
function loadTinymce(selector){
 tinymce.init({
    selector: selector,
    apply_source_formatting : true,
    theme: "modern",
    height: 300,
    relative_urls: false,
    remove_script_host: false,
    valid_elements : '*[*]',
    plugins: [
      "advlist autolink link image lists charmap print preview hr anchor pagebreak",
      "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
      "save table contextmenu directionality emoticons template paste textcolor", "jbimages"
      ],      
      toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons", 
      style_formats: [
     {title: 'Bold text', inline: 'b'},
     {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
     {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
     {title: 'Example 1', inline: 'span', classes: 'example1'},
     {title: 'Example 2', inline: 'span', classes: 'example2'},
     {title: 'Table styles'},
     {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
    ],
    relative_urls : false,      
    
   });    
    
}


/* equal height */
function equalHeight(group) {
  tallest = 0;
  group.each(function () {
    thisHeight = $(this).height();
    if (thisHeight > tallest) {
      tallest = thisHeight;
    }
  });
  group.height(tallest);
}

var room = 1;
function project_task() {
 
    room++;
    var objTo = document.getElementById('project_task')
    var divtest = document.createElement("div");
  divtest.setAttribute("class", "form-group removeclass"+room);
  var rdiv = 'removeclass'+room;
  var first = document.getElementById('project_id');
  var options = first.innerHTML;

  var hours = document.getElementById('hour_id');
  var hours = hours.innerHTML;

  var mins = document.getElementById('min_id');
  var mins = mins.innerHTML;

  var second = document.getElementById('user_id');
	var current_date = $('#current_date').val();
  if(second != null){
    var users = second.innerHTML;
    //divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-2 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="">Select Status</option><option value="1">Completed</option><option value="0">In Progress</option></select></div></div><div class="col-sm-2 nopadding"><div class="form-group"><input type="text" data-required="true" class="form-control" id="total_time" name="total_time[]" value="" placeholder="Enter Total Time"></div></div><div class="col-sm-6 nopadding"><div class="form-group"><textarea rows="3" class="form-control" id="description" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-6 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="ref_link" name="ref_link[]" value="" placeholder="Enter Ref. Link"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="user_id[]" style="width:100%;">'+users+'</select></div></div></div><div class="clear"></div></div>';
    //divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-2 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="">Select Status</option><option value="1">Completed</option><option value="0">In Progress</option></select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="hour[]">'+hours+'</select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;padding-right:10px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="min[]">'+mins+'</select></div></div><div class="col-sm-6 nopadding"><div class="form-group"><textarea rows="3" class="form-control" id="description" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-6 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="ref_link" name="ref_link[]" value="" placeholder="Enter Ref. Link"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="user_id[]" style="width:100%;">'+users+'</select></div></div></div><div class="clear"></div></div>';
divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-2 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="">Select Status</option><option value="1">Completed</option><option value="0">In Progress</option></select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="hour[]">'+hours+'</select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;padding-right:10px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="min[]">'+mins+'</select></div></div><div class="col-sm-6 nopadding"><div class="form-group"><textarea rows="3" class="form-control" id="description" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-6 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="ref_link" name="ref_link[]" value="" placeholder="Enter Ref. Link"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="user_id[]" style="width:100%;">'+users+'</select></div></div></div><div class="col-sm-1 nopadding"><input type="text" name="task_date[]" value="'+current_date+'" class="task_date input-group form-control form-control-inline date date-picker input-small " size="16" data-date-format="dd/mm/yyyy" id="" placeholder="Task Date"></div><div class="clear"></div></div>';    }
 else{
    //divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-2 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="">Select Status</option><option value="1">Completed</option><option value="0">In Progress</option></select></div></div><div class="col-sm-2 nopadding"><div class="form-group"><input type="text" data-required="true" class="form-control" id="total_time" name="total_time[]" value="" placeholder="Enter Total Time"></div></div><div class="col-sm-6 nopadding"><div class="form-group"><textarea rows="3" class="form-control" id="description" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-6 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="ref_link" name="ref_link[]" value="" placeholder="Enter Ref. Link"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div></div><div class="clear"></div></div>';
     divtest.innerHTML = '<div class="add_row_new clearfix"><div class="col-sm-4 nopadding"><div class="form-group"><div class="input-group" style="width:100%;"><select data-required="true" class="form-control" id=" " name="project_id[]" style="width:100%;">'+options+'</select></div></div></div><div class="col-sm-4 nopadding"><div class="form-group"><input type="text" class="form-control" data-required="true" id="title" name="title[]" value="" placeholder="Enter Task Title"></div></div><div class="col-sm-2 nopadding"><div class="form-group"><select class="form-control" id="status" name="status[]" data-required="true"><option value="">Select Status</option><option value="1">Completed</option><option value="0">In Progress</option></select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="hour[]">'+hours+'</select></div></div><div class="col-sm-1 nopadding" style="padding: 0px;margin: 0px;padding-right:10px;"><div class="form-group"><select data-required="true" class="form-control" id=" " name="min[]">'+mins+'</select></div></div><div class="col-sm-6 nopadding"><div class="form-group"><textarea rows="3" class="form-control" id="description" name="description[]" value="" placeholder="Enter Description"></textarea></div></div><div class="col-sm-6 nopadding"><div class="form-group"><div class="input-group"><input type="text" class="form-control" id="ref_link" name="ref_link[]" value="" placeholder="Enter Ref. Link"><div class="input-group-btn" style="padding-left:5px"><button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button></div></div></div></div><div class="clear"></div></div>';
    }
    objTo.appendChild(divtest)
    resetValidator();
}
   function remove_education_fields(rid) {
     $('.removeclass'+rid).remove();
     resetValidator();
   }

   function resetValidator()
   {
    setTimeout(function(){
      $("#main-frm1").parsley().destroy();
      $("#main-frm1").parsley();          
      $(".task_date").datepicker({
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                changeMonth: true,
                yearRange: '1900:2050',
                showButtonPanel: false,
            });
    },400)
   }
