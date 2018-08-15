<?php
    $page = 'investors';
    $scripts = [
        base_url('assets/js/allocations.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var api_url = "<?php echo site_url('allocations'); ?>";
</script>
<div class="container page-title">
    <h1 class="text-center"></h1>
</div>

<div id="main" class="allocation-page">
    <div class="header-div">
        <div class="header-left-div">
            <?php $this->load->view('partials/game-select', array('betweek' => $betweek)); ?>

            <div class="investor-select-div">
                <label>Bet Day</label>
                <select class="select2-large investor-select" name="investor-select" onchange="initPage()">
                    <?php foreach($investors as $investor) {?>
                    <option <?php if($investor['id'] == $investorId) echo "selected";?> value="<?php echo $investor['id']?>"><?php echo $investor['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="pull-right">
            <button type="button" class="btn btn-success" onClick="assignBets()">Assign</button>
        </div>
    </div>
    <div class="allocations-div">
        <table id="allocations" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Current Balance ($)</th>
                    <th>Current Bets</th>
                    <th>( % )</th>
                    <th>Desired ( % )</th>
                    <th>Money Allocation ($)</th>
                    <th>( % )</th>
                    <th>Number of Bets</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>