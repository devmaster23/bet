<?php
    $page = 'sportbooks';
    $scripts = [
        base_url('assets/js/sportbooks.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('sportbooks'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Add New SportBook</h1>
</div>

<div id="main" class="sportbook-page">
    <form method="post" class="form">
        <div class="col-md-12 text-right">
            <input type="hidden" name="id">
            <button type="button" class="btn btn-success" id="back_button">Back</button>
            <button type="submit" name="add_submit" class="btn btn-success">Submit</button>
        </div>
        <div class="row">
            <div class="form-group col-md-6 col-sm-12">
                <label for="title">Title</label>
                <input required type="text" name="title" class="form-control" id="title" placeholder="Enter Title">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="siteurl">SiteURL</label>
                <input required type="text" class="form-control" id="siteurl" name="siteurl" aria-describedby="siteURLHelp" placeholder="Enter SiteURL">
                <small id="siteURLHelp" class="form-text text-muted">This will be used to pull sportbook information.</small>
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="address1">Address1</label>
                <input required type="text" name="address1" class="form-control" id="address1" placeholder="Enter Address1">        
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="address2">Address2</label>
                <input required type="text" name="address2" class="form-control" id="address2" placeholder="Enter Address2">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="city">City</label>
                <input required type="text" name="city" class="form-control" id="city" placeholder="Enter City">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="state">State</label>
                <input required type="text" name="state" class="form-control" id="state" placeholder="Enter State">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="zip_code">Zipcode</label>
                <input required type="text" name="zip_code" class="form-control" id="zip_code" placeholder="Enter Zipcode">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="country">Country</label>
                <input required type="text" name="country" class="form-control" id="country" placeholder="Enter Country">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="phone_number">Phone Number</label>
                <input required type="text" name="phone_number" class="form-control" id="phone_number" placeholder="Enter Phone Number">
            </div>
            <div class="form-group col-md-6 col-sm-12">
                <label for="contact_name">Contact Name</label>
                <input required type="text" name="contact_name" class="form-control" id="contact_name" placeholder="Enter Contact Name" >
            </div>
            <div class="form-group col-md-12 col-sm-12">
                <label for="note">Note</label>
                <textarea rows="5" name="note" class="form-control" id="note" placeholder="Enter Note"></textarea>
            </div>
        </div>
        <div class="row rule-div" style="margin-bottom: 50px;">
            <div class="col-md-4">
                <h3>Singe Bet</h3>
                <div>
                    <label for="singlebet_min">Min</label>
                    <input required type="text" name="singlebet_min" id="singlebet_min">
                </div>
                <div>
                    <label for="singlebet_max">Max</label>
                    <input required type="text" name="singlebet_max" id="singlebet_max">
                </div>
            </div>
            <div class="col-md-4">
                <h3>Parlay</h3>
                <div>
                    <label for="parlay_min_team">Min Number of Teams</label>
                    <input required type="text" name="parlay_min_team" id="parlay_min_team">
                </div>
                <div>
                    <label for="parlay_max_team">Maxium Number of Teams</label>
                    <input required type="text" name="parlay_max_team" id="parlay_max_team">
                </div>
                <div class="mt-3"></div>
                <div>
                    <label for="parlay_min_bet">Min Bet</label>
                    <input required type="text" name="parlay_min_bet" id="parlay_min_bet">
                </div>
                <div>
                    <label for="parlay_max_bet">Max Bet</label>
                    <input required type="text" name="parlay_max_bet" id="parlay_max_bet">
                </div>
            </div>

            <div class="col-md-4">
                <h3>Round Robin</h3>
                <div>
                    <label for="rr_min_team">Min Number of Teams</label>
                    <input required type="text" name="rr_min_team" id="rr_min_team">
                </div>
                <div>
                    <label for="rr_max_team">Maxium Number of Teams</label>
                    <input required type="text" name="rr_max_team" id="rr_max_team">
                </div>
                <div>
                    <label for="rr_max_combination">Maxium Combination</label>
                    <input required type="text" name="rr_max_combination" id="rr_max_combination">
                </div>
                <div class="mt-3"></div>
                <div>
                    <label for="rr_min_bet">Min Bet</label>
                    <input required type="text" name="rr_min_bet" id="rr_min_bet">
                </div>
                <div>
                    <label for="rr_max_bet">Max Bet</label>
                    <input required type="text" name="rr_max_bet" id="rr_max_bet">
                </div>
            </div>
        </div>
    </form>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>