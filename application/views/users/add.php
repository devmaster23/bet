<?php
    $page = 'users';
    $scripts = [
        base_url('assets/js/users.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('users'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Add New User</h1>
</div>
    
<div id="main" class="user-page">
    <form method="post" class="form user-form" style="max-width: 800px; margin: auto;">
        <div class="col-md-12 text-right">
            <button type="button" class="btn btn-success" id="back_button">Back</button>
            <button type="submit" name="add_submit" id="add_submit" class="btn btn-success">Submit</button>
        </div>
        <div class="row">
            <div class="form-group col-md-12 col-sm-12">
                <label for="username">User Name</label>
                <input required type="text" name="username" class="form-control" id="username" placeholder="">
            </div>
            <div class="form-group col-md-12 col-sm-12">
                <label for="name">Full Name</label>
                <input required type="text" name="name" class="form-control" id="name" placeholder="">
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="email">Email</label>
                <input required type="email" name="email" class="form-control" id="email" placeholder="">
            </div>
            <div class="form-group col-md-12 col-sm-12">
                <select class="form-control" name="user_type" required="">
                    <option value="">Select User Type</option>
                    <option value="1">Order Entry</option>
                    <option value="2">Game Entry</option>
                </select>
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="password">Password</label>
                <input required type="password" name="password" class="form-control" id="password" placeholder="" minlength="6">
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="confirm_password">Confirm Password</label>
                <input required type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="" minlength="6">
            </div>
            
        </div>
    </form>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>