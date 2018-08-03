<?php
    $page = 'orders';
    $scripts = [
        base_url('assets/js/orders.js'),
    ];
    $this->load->view('header', array('page' => $page)); 

    if($sportbook_id <= 1)
        $prev_url = './balance?id='.$investor['id'].'&sportbook_id='.$sportbook_count;
    else    
        $prev_url = './balance?id='.$investor['id'].'&sportbook_id='.($sportbook_id-1);
    if($sportbook_id >= $sportbook_count)
        $next_url = './balance?id='.$investor['id'].'&sportbook_id=1';
    else    
        $next_url = './balance?id='.$investor['id'].'&sportbook_id='.($sportbook_id+1);

    date_default_timezone_set('America/Los_Angeles');
    $west_date = date('h:i A');
?>
<script>
    var page_type = 'enter_order';
    var api_url = "<?php echo site_url('orders'); ?>";
</script>

<div id="main" class="order-page no-sub-menu">
    <div class="inner-wrapper enter_order-page">
        <div class="bet-div">
            <div class="bet-div_inner-wrapper">
                <div>
                    <span class="investor_name"><i class="os-icon os-icon-user"></i><?=$investor['full_name']?></span>
                </div>
                <hr/>
                <div>
                    <label>West Coast Time:</label>
                    <span><?=$west_date?></span>
                </div>
                <div>
                    <label>IP Source:</label>
                    <span><?=$ip_source?></span>
                    <?php if($ip_source != $investor['ip']){?>
                        <span class="warn warning"></span>
                    <?php }?>
                </div>
                <div>
                    <label>IP Investor:</label>
                    <span><?=$investor['ip']?></span>
                </div>
                <div>
                    <label>Sportbook:</label>
                    <span><a href="<?=$sportbook['siteurl']?>" target="_blank"><?=$sportbook['siteurl']?></a></span>
                </div>
                <div>
                    <label>Login:</label>
                    <span><?=$sportbook['login_name']?></span>
                </div>
                <div>
                    <label>Password:</label>
                    <span class="red"><?=$sportbook['password']?></span>
                </div>
                <hr/>
                <form  method="post">
                    <div class="sportbook-balance">
                        <div class="element-box no-border-radius gray-top clearfix">
                            <h5 class="form-header">Balance</h5>
                            <input type="text" name="balance" class="input-balance" value="<?=$sportbook['current_balance']?>">
                        </div>
                    </div>

                    <div class="element-box no-border-radius gray-top sportbookList clearfix m-t-20">
                        <div class="clearfix">
                            <div class="text-center">
                                <?php if(!$sportbook_count){ ?>
                                <span class="summary"> No Sportbooks</span>
                                <?php }else{?>
                                <span class="summary"> Sportsbook <?=$sportbook_id?> of <?=$sportbook_count?></span>
                                <?php }?>
                            </div>
                        </div>
                        <?php if($sportbook_count){ ?>
                        <div class="action-div">
                            <a href="<?=$prev_url?>"><img class="prev-img" src="/assets/img/prev.png" /></a>
                            <div>
                                <button type="submit" name="save_balance" class="btn btn-success bet-placed save-button"><img class="camera-img" src="/assets/img/camera_icon.png" /></button>
                            </div>
                            <a href="<?=$next_url?>"><img class="next-img" src="/assets/img/prev.png" /></a>
                            <input type="hidden" name="sportbookID" value="<?=$sportbook['sportbook_id']?>">
                        </div>
                        <div class="sportbookList_inner-wrapper">
                            <table class="table table-striped table-bordered table-hover">
                                <thead style="font-weight: bold;">
                                    <tr>
                                        <th>Title</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($sportbookList as $item) {
                                            $clsSelected = $item['id'] == $sportbook['id'] ? 'selected' : '';
                                    ?>
                                    <tr class="sportbook-tr <?=$clsSelected?>" data-id="<?=$item['sportbook_id']?>">
                                        <td><?=$item['title']?></td>
                                        <td><?=$item['current_balance']?></td>
                                    </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr class="gray">
                                        <td>Total</td>
                                        <td><?=$total_bet?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="iframe-div">
            <?php if($ip_source == $investor['ip']){ ?>
            <iframe src="<?=$iframe_src?>">    
            </iframe>
            <?php } else { ?>
            <span class="vpn-alert"><b>Access is forbidden </b><br/> Check your VPN Connection</span>
            <?php }?>
        </div>
    </div>
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>