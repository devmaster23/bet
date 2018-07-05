<?php
    $page = 'worksheets';
    $scripts = [
        base_url('assets/js/worksheet.js'),
    ];
    $this->load->view('header', array('page' => $page)) ?>
<script>
    var betweek = <?php echo $betweek ?>;
    var settingId = <?php echo $settingId;?>;
    var api_url = "<?php echo site_url('worksheets'); ?>";
    var pageType = "<?php echo $pageType; ?>";
    var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
    <h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main" class="worksheets">
    <div class="header-div">
        <div class="title">
            <span>Bets</span>
        </div>
        <div class="setting-header">
            <p><?=$setting['title']?></p>
        </div>
        <div class="save-button-div">
            <button type="button" class="btn btn-success btn-lg enter-pick_save-button" onClick="updateTable()">Save</button>
        </div>
    </div>
    
    <div class="content-div">
        <div class="row">
            <div class="col-md-4">
                <div>
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
                </div>
                <div class="element-box no-border-radius">
                  <h5 class="form-header">
                    Bet Day Date:
                  </h5>
                  <div class="col-sm-6">
                    <div class="form-group">
                      <div class="date-input">
                        <input class="single-daterange form-control" placeholder="Date of birth" id="betday" type="text" value="">
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="element-box no-border-radius gray-top">
                    <h5 class="form-header">
                        Round Robin
                    </h5>
                    <table id="setting-table" class="table table-editable table-striped table-bordered table-lightfont editableTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width: 13%">A</th>
                                <th style="width: 13%">B</th>
                                <th style="width: 13%">C</th>
                                <th style="width: 13%">D</th>
                                <th style="width: 13%">E</th>
                                <th style="width: 13%">F</th>
                                <th style="width: 13%">G</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="row roundrobbin-div">
                        <div class="col-sm-2">
                            <a class="element-box el-tablo" href="javascript:;">
                                <div id="rr1" class="value"></div>
                            </a>
                        </div>
                        <div class="col-sm-2">
                          <a class="element-box el-tablo" href="javascript:;">
                                <div id="rr2" class="value"></div>
                            </a>
                        </div>
                        <div class="col-sm-2">
                          <a class="element-box el-tablo" href="javascript:;">
                                <div id="rr3" class="value"></div>
                            </a>
                        </div>
                        <div class="col-sm-2">
                          <a class="element-box el-tablo" href="javascript:;">
                                <div id="rr4" class="value"></div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bets">
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
                          <tr>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                          </tr>
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
                          <tr>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                            <td>1</td>
                          </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>