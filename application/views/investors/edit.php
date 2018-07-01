<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/investors.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var page_type = 'edit';
    var api_url = "<?php echo site_url('investors'); ?>";
    var investor = <?php echo json_encode($investor)?>;
    var sportbookList = <?php echo json_encode($sportbook_list) ?>;
    var userSportbookList = investor.sportbooks;
</script>
<div class="container page-title">
    <h1 class="text-center">Edit Investor Information</h1>
</div>

<div id="main" class="investor-page">
    <form method="post" class="form">
        <div class="col-md-12 text-right">
            <input type="hidden" name="id" value="<?=$investor['id']?>">
            <textarea name="sportbook_data" style="display: none;"></textarea>
            <button type="button" class="btn btn-success" id="back_button">Back</button>
            <button type="submit" name="edit_submit" id="edit_submit" class="btn btn-success" >Update</button>
        </div>
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                <label for="first_name">First Name</label>
                <input required type="text" name="first_name" class="form-control" id="first_name" placeholder="Enter First Name" value="<?=$investor['first_name']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="last_name">Last Name</label>
                <input required type="text" name="last_name" class="form-control" id="last_name" placeholder="Enter Last Name" value="<?=$investor['last_name']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="last_name">User Group</label>
                <select class="form-control" name="group_id">
                    <?php foreach ($group_list as $group) { ?>
                    <option value="<?=$group['id']?>" <?=$investor['group_id'] == $group['id'] ? 'selected': ''; ?> ><?=$group['name']?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="email">Email</label>
                <input required type="text" name="email" class="form-control" id="email" placeholder="Enter Email" value="<?=$investor['email']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="ip">IP Address</label>
                <input required type="text" name="ip" class="form-control" id="ip" placeholder="IP Address e.g (100.100.100.100)" value="<?=$investor['ip']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="phone_number">Phone Number</label>
                <input required type="text" name="phone_number" class="form-control" id="phone_number" placeholder="Enter Phone Number" value="<?=$investor['phone_number']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="address1">Address1</label>
                <input required type="text" name="address1" class="form-control" id="address1" placeholder="Enter Address1" value="<?=$investor['address1']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="address2">Address2</label>
                <input required type="text" name="address2" class="form-control" id="address2" placeholder="Enter Address2" value="<?=$investor['address2']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="city">City</label>
                <input required type="text" name="city" class="form-control" id="city" placeholder="Enter City" value="<?=$investor['city']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="state">State</label>
                <input required type="text" name="state" class="form-control" id="state" placeholder="Enter State" value="<?=$investor['state']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="zip_code">Zipcode</label>
                <input required type="text" name="zip_code" class="form-control" id="zip_code" placeholder="Enter Zipcode" value="<?=$investor['zip_code']?>">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="country">Country</label>
                <input required type="text" name="country" class="form-control" id="country" placeholder="Enter Country" value="<?=$investor['country']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="starting_bankroll">Starting BankRoll</label>
                <input required type="text" name="starting_bankroll" class="form-control" id="starting_bankroll" placeholder="Enter Starting BankRoll" value="<?=$investor['starting_bankroll']?>">
            </div>

            <div class="form-group col-md-6 col-sm-12">
                <label for="current_balance">Current Balance</label>
                <span class="info" id="current_balance">$ <?=$investor['current_balance']?></span>
                <small id="currentBalanceHelp" class="form-text text-muted">Sum of all sportsbooks current balance.</small>
            </div>
            
            <div class="form-group col-md-12 col-sm-12">
                <label for="note">Note</label>
                <textarea rows="5" name="note" class="form-control" id="note" placeholder="Enter Note"><?=$investor['note']?></textarea>
            </div>
        </div>
        <div class="row">
            <h3 class="ml-3">List of SportBooks</h3>
            <div class="col-md-12 text-left">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newSportBookModal" onclick="openSportBookModal()">
                    Add New SportBook
                </button>

            </div>
            <div id="sportbook_list" class="mt-3 ml-3" style="margin-bottom: 100px;">
                
            </div>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="newSportBookModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Select SportBook Item</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Title</th>
                    <th>URL</th>
                    <th>Country</th>
                    <th>Contact Name</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach($sportbook_list as $sportbook_item)
            {
            ?>
                <tr data-id="<?=$sportbook_item['id']?>">
                    <td><input type="checkbox" name="sportbook_check"></td>
                    <td><span><?=$sportbook_item['title']?></span></td>
                    <td><span><?=$sportbook_item['siteurl']?></span></td>
                    <td><span><?=$sportbook_item['country']?></span></td>
                    <td><span><?=$sportbook_item['contact_name']?></span></td>
                </tr>
            <?php
            }?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary"  data-dismiss="modal" onclick="addSportBook()">Select</button>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>