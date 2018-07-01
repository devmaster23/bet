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
    <h1 class="text-center">User List</h1>
</div>

<div id="main" class="user-page" style="max-width: 1000px; margin: auto">
    <div class="row">
        <div class="col-md-12 text-right mb-3">
            <a href="<?=site_url('users');?>/add" name="edit_submit" class="btn btn-success">Add New</a>
        </div>
    </div>
    <div>
        <table id="users" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>UserName</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th></th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<?php $this->load->view('footer', array('scripts' => $scripts)) ?>