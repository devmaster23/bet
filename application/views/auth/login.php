<!DOCTYPE html>
<html>
  <head>
    <title>Bet Game</title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="template language" name="keywords">
    <meta content="Tamerlan Soziev" name="author">
    <meta content="Admin dashboard html template" name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <link href="favicon.png" rel="shortcut icon">
    <link href="apple-touch-icon.png" rel="apple-touch-icon">
    <link href='<?php echo base_url('assets/css/theme.css') ?>' rel='stylesheet' />
  </head>
  <body class="auth-wrapper">
    <div class="all-wrapper menu-side with-pattern">
      <div class="auth-box-w">
        <div class="logo-w">
          <a href="#"><img alt="" src="/assets/img/logo_big.png"></a>
        </div>
        <h4 class="auth-header">
          Login Form
        </h4>
        <form role="form" method="post" action="" class="login-form">

          <?php 
          $error_msg=$this->session->flashdata('error_msg');
          if($error_msg){
          ?>
            <div class="alert alert-danger" role="alert">
              <?=$error_msg?>
            </div>
          <?php }?>
          <div class="form-group">
            <label for="">Username</label><input class="form-control" placeholder="Enter your username" type="text" name="username" required="">
            <div class="pre-icon os-icon os-icon-user-male-circle"></div>
          </div>
          <div class="form-group">
            <label for="">Password</label><input class="form-control" placeholder="Enter your password" type="password" name="password" id="password">
            <div class="pre-icon os-icon os-icon-fingerprint"></div>
          </div>
          <div class="buttons-w">
            <div class="form-check" style="margin-bottom: 10px;">
              <label class="form-check-label"><input class="form-check-input" type="checkbox" id="show_password">Show Password</label>
            </div>
            <button class="btn btn-primary" type="submit" name="login">Log in</button>
          </div>
        </form>
      </div>
    </div>
  <script src='<?php echo base_url('assets/vendor/js/jquery.min.js') ?>'></script>
  <script src='<?php echo base_url('assets/vendor/js/bootstrap.min.js') ?>'></script>
  <script src='<?php echo base_url('assets/js/auth.js') ?>'></script>
  </body>
</html>
