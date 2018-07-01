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
    <h1 class="text-center">Edit User Info</h1>
</div>
    
<div id="main" class="user-page">
    <form method="post" class="form user-form" style="max-width: 800px; margin: auto;">
        <div class="col-md-12 text-right">
            <input type="hidden" name="id" value="<?php echo $user['id']?>">
            <button type="button" class="btn btn-success" id="back_button">Back</button>
            <button type="submit" name="edit_submit" id="add_submit" class="btn btn-success">Submit</button>
        </div>
        <div class="row">
            <div class="form-group col-md-12 col-sm-12">
                <label for="username">User Name</label>
                <input required type="text" name="username" class="form-control" id="username" placeholder="" value="<?php echo $user['username']?>">
            </div>
            <div class="form-group col-md-12 col-sm-12">
                <label for="name">Full Name</label>
                <input required type="text" name="name" class="form-control" id="name" placeholder="" value="<?php echo $user['name']?>">
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="email">Email</label>
                <input required type="email" name="email" class="form-control" id="email" placeholder="" value="<?php echo $user['email']?>">
            </div>
            <div class="form-group col-md-12 col-sm-12">
                <label for="usertype">UserType</label>
                <select class="form-control" name="user_type" required="">
                    <option value="">Select User Type</option>
                    <option value="1" <?php echo $user['user_type'] == 1 ? 'selected' : ''?> >Order Entry</option>
                    <option value="2" <?php echo $user['user_type'] == 2 ? 'selected' : ''?>>Game Entry</option>
                </select>
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="" minlength="6">
                <i>Leave Blank if not changed</i>
            </div>

            <div class="form-group col-md-12 col-sm-12">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="" minlength="6">
            </div>
            
        </div>
    </form>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>