<?php
    $page = 'picks';
    $scripts = [
        base_url('assets/js/picks.sheet.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var betweek = <?php echo $betweek ?>;
    var api_url = "<?php echo site_url('picks'); ?>";
    var pageType = "<?php echo $pageType; ?>";
    var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
    <h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main" class="picks">
    <div class="header-div">
        <div class="game-week-select-div">
            <label>Bet Day</label>
            <i class="fa fa-chevron-left bet-week-prev"></i>
            <select class="select2 game-week-select" name="game-week-select" onchange="initPage()">
                <?php for($i=1; $i<=60; $i++) {?>
                <option <?php if($i == $betweek) echo "selected";?> value="<?php echo $i?>"><?php echo $i?></option>
                <?php }?>
            </select>
            <i class="fa fa-chevron-right bet-week-next"></i>
        </div>
        <div class="save-button-div">
            <button type="button" class="btn btn-success btn-lg enter-pick_save-button" onClick="updateTable()">Update</button>
        </div>
    </div>

    <div class="content-div">
        <div class="tab-content">
        <?php if ($pageType == 'all_picks') {?>
            <div class="row all-picks">
                <div class="col-md-4">
                    <div class="element-box no-border-radius green-top">
                      <h5 class="form-header">
                        Wrapper
                      </h5>
                      <table class="table table-striped" id="wrapper-table">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Sport</th>
                            <th>VRN</th>
                            <th>SP/ML<br/>(Ov/Un)</th>
                            <th>Team</th>
                            <th>Line</th>
                            <th>Game<br/>Time</th>
                            <th>Co.</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="element-box no-border-radius red-top">
                      <h5 class="form-header">
                        Candy
                      </h5>
                        <table class="table table-striped" id="candy-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Sport</th>
                                <th>VRN</th>
                                <th>SP/ML<br/>(Ov/Un)</th>
                                <th>Team</th>
                                <th>Line</th>
                                <th>Game<br/>Time</th>
                                <th>Co.</th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="element-box no-border-radius blue-top">
                      <h5 class="form-header">
                        Pick
                      </h5>
                      <table class="table table-striped" id="pick-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Sport</th>
                                <th>VRN</th>
                                <th>SP/ML<br/>(Ov/Un)</th>
                                <th>Team</th>
                                <th>Line</th>
                                <th>Game<br/>Time</th>
                                <th>Co.</th>
                              </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } else {?>
            <div class="pick-game" id="<?php echo $pageType?>">
                <div class="sheet">
                </div>
            </div>
        <?php }?>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>