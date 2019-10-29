var value_tmp = [
   "id",
   "username",
   "email",
   "descript",
   "status",
   { name: "edited", attr: "data-edited" },
 ];

var list_options = {
  valueNames: value_tmp,
  item: '<tr>' 
    + '<th class="id" scope="row"></th>'
    + '<td class="username"></td>'
    + '<td class="email"></td>'
    + '<td class="descript"></td>'
    + '<td class="status"></td>'
    + '<td class="edited"></td>'
  + '</tr>',
  page: 3,
  pagination: [{
    paginationClass: "paginationBottom",
    innerWindow: 2,
    left: 2,
    right: 2
  }],
};

var new_val = '<tr>'
  + '<form id="form_new" action="" enctype="multipart/form-data" method="post">'
  + '<th class="id" scope="row"></th>'
  + '<td class="username"><input id="username" name="username" class="form-control" type="text" placeholder="*Укажите имя"></td>'
  + '<td class="email"><input id="email" name="email" class="form-control" type="text" placeholder="*Укажите email"></td>'
  + '<td class="descript"><input id="descript" name="descript" class="form-control desc" type="text" placeholder="*Кратко опишите суть задачи"></td>'
  + '<td class="status"></td>'
  + '<td class="edited"></td>'
+ '</form></tr>';

$(document).ready(function(){
  var listjs = new List("userlist", list_options, values);
});

$(".add-new").on("click", function() {
  $(".list").prepend(new_val);
  $(".add-new").remove();
  $("button.save").css("display", "initial");
  $("button.sort").contents().unwrap(); // Disable sort
  $("input").on("change paste", function() {
    if ( $("input#username").val() && $("input#email").val() && $("input#descript").val() )
      $("button.save").prop("disabled", false).removeAttr("aria-disabled");
    else
      $('button.save').prop("disabled", true).prop("aria-disabled", true)
  });
});

$("button.save").on("click", function() {
  $.ajax(options);
});

var serialized = $("#form_new").serializeArray();
var response = '';
var options = {
  type: 'POST',
  url: 'index.php',
  data: serialized,
  dataType: 'text',
  success : function(text) {
    response = text;
    if (response == "done")
      window.location = "http://" + siteurl + "/";
    else {
      alert( response );
      console.log( response );
    }
  },
  error: function(text) {
    response = text;
    alert( response );
  }
};