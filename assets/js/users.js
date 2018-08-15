$(document).ready(function() {
    var table = $('#users').DataTable( {
        "ajax": api_url+"/user_list",
        "columns": [
            { "data": "index" },
            { "data": "username" },
            { "data": "name" },
            { "data": "email" },
            { "data": "type" },
            { "data": "custom_action" }
        ],
        "order": [[0, 'asc']]
    } );
     
    $('#profile_img_button').on('click', function(){

        $("input[name='profile_img']").click();
    })

    $("input[name='profile_img']").on('change',function(){
        if (this.files && this.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#profile_img_preview').show().attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);
        }
    });

    $('#users tbody').on('click', '.delete', function () {
        var id = $(this).parents('.action-div').data('id');
        if(confirm("Are you sure you want to remove this sportbook?"))
        {
            $.ajax({
                url: api_url+'/delete',
                type: 'POST',
                data: {
                  id: id
                },
                success: function(data) {
                    location.href = api_url;
                }
            });
        }
    } );

    $("#back_button").click(function(){
        location.href = api_url;
    })

    var password = document.getElementById("password")
      , confirm_password = document.getElementById("confirm_password");

    function validatePassword(){
      if(password.value != confirm_password.value) {
        confirm_password.setCustomValidity("Passwords Don't Match");
      } else {
        confirm_password.setCustomValidity('');
      }
    }
    if(password){
        password.onchange = validatePassword;
        confirm_password.onkeyup = validatePassword;
    }
} );