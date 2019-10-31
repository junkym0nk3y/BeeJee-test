var new_val = '<tr><th class="id" scope="row" style="color: #007bff">new</th>'
    + '<td class="username"><input id="username" name="username" class="form-control" type="text" placeholder="*Укажите имя"></td>'
    + '<td class="email"><input id="email" name="email" class="form-control" type="text" placeholder="*Укажите email"></td>'
    + '<td class="descript"><input id="descript" name="descript" class="form-control desc" type="text" placeholder="*Кратко опишите суть задачи"></td>'
    + '<td class="hint" colspan="2">Заполните все поля</td></tr>';

$( '.add-new' ).on( 'click', function() {
  $( '#table').append( '<input type="hidden" name="new_task" value="1">' ).wrap( ''
    + '<form id="new_task" action="" enctype="multipart/form-data" method="post"></form>' );
  $( '.list' ).prepend( new_val );
  $( '.add-new, .pagesBottom' ).remove();
  $( 'button.save' ).css( 'display', 'initial' );
  $( 'button.sort' ).contents().unwrap(); // Disable sort
  $( 'input').on( 'input paste', function() {
    if ( $('input#username').val() && $('input#email').val() && $('input#descript').val() ){
      $( 'button.save' ).prop( 'disabled', false ).removeAttr( 'aria-disabled' );
      $( 'td.hint' ).css( 'color', 'green' );
    } else {
      $( 'button.save' ).prop( 'disabled', true ).prop( 'aria-disabled', true );
      $( 'td.hint' ).css( 'color', 'red' );
    }
  });
});

$( 'button.save' ).on( 'click', function() {
  var queryString = $( 'form#new_task' ).formSerialize();
  var response = '';
  var options = {
    type: 'POST',
    url: 'index.php',
    data: queryString,
    dataType: 'text',
    success : function( text ) {
      response = text;
      if ( response == 'done' )
        document.location.href='/';
      else
        alert( response );
    },
    error: function(text) {
      response = text;
      alert( response );
    }
  };
  
  $.ajax(options);
});


var value_tmp = [
   'id',
   'username',
   'email',
   'descript',
   'status',
   { name: 'edited', attr: 'data-edited' },
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
    paginationClass: 'paginationBottom',
    innerWindow: 2,
    left: 2,
    right: 2
  }],
};

$(document).ready(function(){
  var listjs = new List( 'userlist', list_options, values );
});
