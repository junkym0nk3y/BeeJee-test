/* Form template */
var new_val = '<tr><th class="align-middle id" scope="row" style="color: #007bff">new</th>'
    + '<td class="align-middle username"><input id="username" name="username" class="form-control" type="text" placeholder="*Укажите имя"></td>'
    + '<td class="align-middle email"><input id="email" name="email" class="form-control" type="text" placeholder="*Укажите email"></td>'
    + '<td class="align-middle descript"><input id="descript" name="descript" class="form-control desc" type="text" placeholder="*Кратко опишите суть задачи"></td>'
    + '<td class="align-middle hint" colspan="2">Заполните все поля</td></tr>';


/* Add new task */
$( '.add-new' ).on( 'click', function() {
  $( '#table').append( '<input type="hidden" name="new_task" value="1">' ).wrap( ''
    + '<form id="new_task" action="" enctype="multipart/form-data" method="post"></form>' );
  $( '.list' ).prepend( new_val );
  $( '.add-new, .pagesBottom' ).remove();
  $( 'button.save' ).css( 'display', 'initial' );
  $( 'button.sort' ).contents().unwrap(); // Disable sort
  $( 'input#username' ).add( 'input#email' ).add( 'input#descript' ).on( 'input paste', function() {
    if ( $('input#username').val() && $('input#email').val() && $('input#descript').val() ){
      $( 'button.save' ).prop( 'disabled', false ).removeAttr( 'aria-disabled' );
      $( 'td.hint' ).css( 'color', 'green' );
    } else {
      $( 'button.save' ).prop( 'disabled', true ).prop( 'aria-disabled', true );
      $( 'td.hint' ).css( 'color', 'red' );
    }
  });
});


/* Send json data */
$( 'button.save' ).on( 'click', function() {
  var queryString = $( 'form#new_task' ).formSerialize();
  var response = '';
  var options = {
    type: 'POST',
    url: 'index.php',
    data: queryString,
    dataType: 'text',
    success : function( text ) {
      response = $.trim(text);
      if ( response == 'done' ){
        swal.fire({
          title: 'Добавлена новая задача!',
          type: 'success',
          showConfirmButton: false,
          showCancelButton: false,
          timer: 1500
        }).then(response => {
          window.location = window.location.href;
        })
      }
      else if ( response == 'updated' )
        window.location = window.location.href;
      else {
        swal_default.text = response;
        swal.fire(swal_default);
      }
    },
    error: function(text) {
      response = $.trim(text);
      alert( response );
    }
  };
  
  $.ajax(options);
});


/* List.js init */
var value_tmp = [
   'id',
   'username',
   'email',
   'descript',
   'status',
   'edited',
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


/* Modal */
var swal_default = {
  title: 'Упс...',
  text: '',
  type: 'error',
  showConfirmButton: false,
  showCancelButton: true,
  cancelButtonColor: '#e72365',
  cancelButtonText: 'Я попробую снова!'
};


/* Logon Modal */
$( 'button.auth' ).on( 'click', function() {
  swal.fire({
    title: '<h1>Login</h1>',
    allowOutsideClick: true,
    allowEscapeKey: true,
    showCancelButton: false,
    showConfirmButton: false,
    cancelButtonColor: '#e72365',
    width: 300,
    html: ''
      + '<form id="auth" class="login-form" method="post" action="">'
        + '<input type="hidden" name="auth" value="1">'
        + '<div class="control-group">'
          + '<input type="text" name="login" id="login-name" class="login-field" value=""'
            + 'placeholder="Логин" maxlength="32" title="Используйте только латиницу и цифры" required>'
          + '<label class="login-field-icon fui-user" for="login-name"></label>'
        + '</div>'
        + '<div class="control-group">'
          + '<input type="password" name="password" id="login-pass" class="login-field" value="" placeholder="Пароль" >'
          + '<label class="login-field-icon fui-lock" for="login-pass"></label>'
        + '</div>'
      + '</form>'
      + '<button type="button" class="btn btn-primary btn-block" disabled>Войти</button>'
  });


/* Login form */
  $('input#login-name').add('input#login-pass').on('input paste', function() {
    var val_text = $('input#login-name').val();
    regex = val_text.trim().replace(/[^a-z0-9\s\-]/gi, '');
    $('input#login-name').val( regex );

    if ( $('input#login-name').val() && $('input#login-pass').val() )
      $( 'button.btn-block' ).prop( 'disabled', false ).removeAttr( 'aria-disabled' );
    else
      $( 'button.btn-block' ).prop( 'disabled', true ).prop( 'aria-disabled', true );
  });


/* Enter key */
  $(document).keypress(function (e) {
    if (e.which == 13) {
      $( 'button.btn-block' ).click();
    };
  });


/* Send login form */
  $( 'button.btn-block' ).on( 'click', function() {
    var queryString = $( 'form#auth' ).formSerialize();
    var response = '';
    var options = {
      type: 'POST',
      url: 'index.php',
      data: queryString,
      dataType: 'text',
      success : function( text ) {
        response = $.trim(text);
        if ( response == 'done' )
          document.location.href='/admin';
        else {
          swal_default.text = response;
          swal.fire(swal_default);
        }
      },
      error: function(text) {
        response = $.trim(text);
        alert( response );
      }
    };
    
    $.ajax( options );
  });
});
