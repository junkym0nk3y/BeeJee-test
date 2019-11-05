/* Logoff user */
function logoff(){
  Cookies.remove( 'user_login', { path: '/', domain: '.' + domain });
  Cookies.remove( 'hash', { path: '/', domain: '.' + domain });
  window.location.href = '/admin';
}

$( 'button.logoff' ).on( 'click', function() {
  logoff();
});


/* User session timer */
function startTimer( duration ) {
  var timer = duration -1, minutes, seconds;
  setInterval(function () {
    minutes = parseInt(timer / 60, 10)
    seconds = parseInt(timer % 60, 10);
  
    minutes = minutes < 10 ? "0" + minutes : minutes;
    seconds = seconds < 10 ? "0" + seconds : seconds;
  
    $('#countdown span').text( minutes + ":" + seconds );
  
    if (--timer < 0) {
      logoff();
    }
  }, 1000);
}

$(document).ready(function(){
  startTimer( cookie_time );

  /* Editor's template */
  list_options['item'] = '<tr>' 
    + '<th class="align-middle id" scope="row"></th>'
    + '<td class="align-middle username"></td>'
    + '<td class="align-middle email"></td>'
    + '<td class="align-middle descript"></td>'
    + '<td class="align-middle status"></td>'
    + '<td class="align-middle edited"><i class="material-icons edit">edit</i></td>'
  + '</tr>';

  /* List.js value list */
  list_options[ 'valueNames' ] = [
    'id',
    'username',
    'email',
    'descript',
    'status',
    { name: 'edited', attr: 'data-edited' },
  ]

  /* List.js init */
  var listjs = new List( 'userlist', list_options, values );


  /* Edit button */
  $( '.edit' ).on( 'click', function() {
    $( '#table').prepend( '<input type="hidden" name="admin" value="1">'
      + '<input type="hidden" name="status" value="0">' ).wrap( ''
      + '<form id="new_task" action="" enctype="multipart/form-data" method="post"></form>' );
    $( 'button.save' ).css( 'display', 'initial' );
    $( 'button.save' ).prop( 'disabled', false ).removeAttr( 'aria-disabled' );
    $( 'button.sort' ).contents().unwrap(); // Disable sort
    $( '.edit' ).off();
    var id = $(this).parents('tr').find('.id');
    var id_text = id.text();
    $( '#table').prepend('<input type="hidden" name="id" value="' + id_text + '">');

    var descript = $(this).parents('tr').find('.descript');
    var desc_text = descript.text();
    var desc_input = '<input id="descript" name="descript" class="form-control desc" type="text"></td>';
    descript.empty().append(desc_input);
    $('#descript').val(desc_text);

    var status = $(this).parents('tr').find('.status');
    var status_text = status.text();
    set_stat = status.text() ? 'checked' : '';
    var status_input = '<input class="form-check-input" type="checkbox" name="status" value="1" id="status"' + set_stat
      + '><label class="form-check-label" for="status">Выполнено</label>';
    status.empty().append(status_input);

    $( 'input#descript' ).on( 'input paste', function() {
      if ( $(this).val() )
        $( 'button.save' ).prop( 'disabled', false ).removeAttr( 'aria-disabled' );
      else 
        $( 'button.save' ).prop( 'disabled', true ).prop( 'aria-disabled', true );
    });
  });

});
