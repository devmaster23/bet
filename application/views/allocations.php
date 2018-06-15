<?php
    $page = 'allocations';
    $scripts = [
        base_url('assets/js/allocations.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('allocations'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center">Allocation of Money</h1>
</div>

<div id="main" class="sportbook-page">
    <div class="header-div">
        <div class="header-left-div">
            <div class="game-week-select-div">
                <label>Bet Day</label>
                <select class="select2 game-week-select" name="game-week-select" onchange="initPage()">
                    <?php for($i=1; $i<=60; $i++) {?>
                    <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                    <?php }?>
                </select>
            </div>

            <div class="investor-select-div">
                <label>Bet Day</label>
                <select class="select2-large investor-select" name="investor-select" onchange="initPage()">
                    <?php foreach($investors as $investor) {?>
                    <option <?php if($investor['id'] == $investorId) echo "selected";?> value="<?php echo $investor['id']?>"><?php echo $investor['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-right mb-3">
            <button type="button" class="btn btn-success" onClick="assignBets()">Assign</button>
        </div>
    </div>
    
    <table id="allocations" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Title</th>
                <th>Current Balance</th>
                <th>Current Bets</th>
                <th>( % )</th>
                <th>Desired ( % )</th>
                <th>Money Allocation</th>
                <th>( % )</th>
                <th>Number of Bets</th>
            </tr>
        </thead>
    </table>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>