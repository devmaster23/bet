$('.login-form #show_password').on('click', function(){
var passwordObj = $('.login-form #password');
if ($(this).is(':checked')) {
    passwordObj.attr('type', 'text');
} else {
    passwordObj.attr('type', 'password');
}
});