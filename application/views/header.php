<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BetGame -
        <?php echo $page; ?>
    </title>
    <link href='<?php echo base_url('assets/vendor/css/bootstrap.min.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('bower_components/select2/dist/css/select2.min.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/vendor/css/handsontable.full.min.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/DataTables/datatables.min.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/style.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/setting.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/sportbooks.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/investors.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/orders.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/users.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/css/theme.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/vendor/font-awesome/css/font-awesome.min.css') ?>' rel='stylesheet' />
    <link href='<?php echo base_url('assets/vendor/bootstrap-daterangepicker/daterangepicker.css') ?>' rel='stylesheet' />
</head>
<?php
  $userInfo = $this->authlibrary->userInfo();
?>
<body class="full-screen with-content-panel menu-position-top">
    <div class="all-wrapper with-side-panel">
        <div class="layout-w">
            <!--------------------
    START - Mobile Menu
    -------------------->
            <div class="menu-mobile menu-activated-on-click color-scheme-dark">
                <div class="mm-logo-buttons-w">
                    <a class="mm-logo" href="index.html"><img src="assets/img/logo.png"><span>Clean Admin</span></a>
                    <div class="mm-buttons">
                        <div class="mobile-menu-trigger">
                            <div class="os-icon os-icon-hamburger-menu-1"></div>
                        </div>
                    </div>
                </div>
                <div class="menu-and-user">
                    <div class="logged-user-w">
                        <div class="avatar-w">
                            <img alt="" src="<?php echo base_url('assets/img/avatar1.jpg')?>">
                        </div>
                        <div class="logged-user-info-w">
                            <div class="logged-user-name">
                                <?php echo $userInfo['name']?>
                            </div>
                            <div class="logged-user-role">
                                <?php echo $userInfo['user_role']?>
                            </div>
                        </div>
                    </div>
                    <!--------------------
        START - Mobile Menu List
        -------------------->
                    <ul class="main-menu">
                        <li class="has-sub-menu">
                            <a href="index.html">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-layout"></div>
                                </div>
                                <span>Game</span></a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="index.html">NCAA M</a>
                                </li>
                                <li>
                                    <a href="apps_crypto.html">NBA</a>
                                </li>
                                <li>
                                    <a href="apps_support_dashboard.html">NFL</a>
                                </li>
                                <li>
                                    <a href="apps_projects.html">NCAA F</a>
                                </li>
                                <li>
                                    <a href="apps_bank.html">Soccer</a>
                                </li>
                                <li>
                                    <a href="layouts_menu_top_image.html">MLB</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub-menu">
                            <a href="layouts_menu_top_image.html">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-layers"></div>
                                </div>
                                <span>Picks</span></a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="layouts_menu_side_full.html">All Picks</a>
                                </li>
                                <li>
                                    <a href="layouts_menu_side_full_dark.html">NCAA M</a>
                                </li>
                                <li>
                                    <a href="layouts_menu_side_transparent.html">NBA</a>
                                </li>
                                <li>
                                    <a href="apps_pipeline.html">NCAA F</a>
                                </li>
                                <li>
                                    <a href="apps_projects.html">Soccer</a>
                                </li>
                                <li>
                                    <a href="layouts_menu_side_mini.html">MLB</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub-menu">
                            <a href="apps_bank.html">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-package"></div>
                                </div>
                                <span>Worksheet</span></a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="apps_email.html">Summary</a>
                                </li>
                                <li>
                                    <a href="apps_support_dashboard.html">Bets</a>
                                </li>
                                <li>
                                    <a href="apps_support_index.html">RR & Parlay</a>
                                </li>
                                <li>
                                    <a href="apps_crypto.html">Picks</a>
                                </li>
                                <li>
                                    <a href="apps_projects.html">Custom</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub-menu">
                            <a href="#">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-file-text"></div>
                                </div>
                                <span>Control Panel</span></a>
                        </li>
                        <li class="has-sub-menu">
                            <a href="#">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-life-buoy"></div>
                                </div>
                                <span>Investors</span></a>
                            <ul class="sub-menu">
                                <li>
                                    <a href="uikit_modals.html">Investor</a>
                                </li>
                                <li>
                                    <a href="uikit_alerts.html">Money Allocation</a>
                                </li>
                            </ul>
                        </li>
                        <li class="has-sub-menu">
                            <a href="#">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-mail"></div>
                                </div>
                                <span>SportBooks</span></a>
                        </li>
                        <li class="has-sub-menu">
                            <a href="#">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-users"></div>
                                </div>
                                <span>Orders</span></a>
                        </li>

                        <li class="has-sub-menu">
                            <a href="#">
                                <div class="icon-w">
                                    <div class="os-icon os-icon-users"></div>
                                </div>
                                <span>Users</span></a>
                        </li>
                    </ul>
                    <!--------------------
        END - Mobile Menu List
        -------------------->
                </div>
            </div>
            <!--------------------
    END - Mobile Menu
    -------------------->
            <!--------------------
    START - Main Menu
    -------------------->
            <div class="menu-w selected-menu-color-light menu-activated-on-hover menu-has-selected-link color-scheme-dark color-style-bright sub-menu-color-bright menu-position-top menu-layout-compact sub-menu-style-over">
                <div class="logo-w">
                    <a class="logo" href="/">
                        <div class="logo-label">
                            <img src="<?php echo base_url('assets/img/header_logo.png')?>">
                        </div>
                    </a>
                </div>
                <div class="logged-user-w avatar-inline">
                    <div class="logged-user-i">
                        <div class="avatar-w">
                            <img alt="" src="<?php echo base_url('assets/img/avatar1.jpg')?>">
                        </div>
                        <div class="logged-user-info-w">
                            <div class="logged-user-name">
                                <?php echo $userInfo['name']?>
                            </div>
                            <div class="logged-user-role">
                                <?php echo $userInfo['user_role']?>
                            </div>
                        </div>
                        <div class="logged-user-toggler-arrow">
                            <div class="os-icon os-icon-chevron-down"></div>
                        </div>
                        <div class="logged-user-menu color-style-bright">
                            <div class="logged-user-avatar-info">
                                <div class="avatar-w">
                                    <img alt="" src="<?php echo base_url('assets/img/avatar1.jpg')?>">
                                </div>
                                <div class="logged-user-info-w">
                                    <div class="logged-user-name">
                                        <?php echo $userInfo['name']?>
                                    </div>
                                    <div class="logged-user-role">
                                        <?php echo $userInfo['user_role']?>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-icon">
                                <i class="os-icon os-icon-wallet-loaded"></i>
                            </div>
                            <ul>
                                
                                <li>
                                    <a href="#"><i class="os-icon os-icon-user-male-circle2"></i><span>Profile Details</span></a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('logout'); ?>"><i class="os-icon os-icon-signs-11"></i><span>Logout</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <ul class="main-menu">
                    <li class="sub-header">
                        <span>Layouts</span>
                    </li>

                    <?php if(in_array($userInfo['user_type'], [0,2])) { ?>
                    <li class="has-sub-menu <?php echo ($page == 'games')?'selected':'';?>">
                        <a href="<?php echo site_url('games'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-layout"></div>
                            </div>
                            <span>Games</span></a>
                        <div class="sub-menu-w">
                            <div class="sub-menu-header">
                                Games
                            </div>
                            <div class="sub-menu-icon">
                                <i class="os-icon os-icon-layout"></i>
                            </div>
                            <div class="sub-menu-i">
                                <ul class="sub-menu">
                                    <li class="<?php echo ($page == 'games' && $pageType == 'football')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=football'); ?>"><img src="<?php echo base_url('assets/img/football_icon.png')?>">NFL</a>
                                    </li>
                                    <li class="<?php echo ($page == 'games' && $pageType == 'ncaa_f')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=ncaa_f'); ?>"><img src="<?php echo base_url('assets/img/icon_NFL.png')?>">NCAA F</a>
                                    </li>
                                    <li class="<?php echo ($page == 'games' && $pageType == 'nba')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=nba'); ?>"><img src="<?php echo base_url('assets/img/icon_NBA.png')?>">NBA</a>
                                    </li>
                                    <li class="<?php echo ($page == 'games' && $pageType == 'ncaa_m')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=ncaa_m'); ?>"><img src="<?php echo base_url('assets/img/icon_NCAAM.png')?>">NCAA M</a>
                                    </li>
                                    <li class="<?php echo ($page == 'games' && $pageType == 'soccer')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=soccer'); ?>"><img src="<?php echo base_url('assets/img/icon_soccer.png')?>">Soccer</a>
                                    </li>
                                    <li class="<?php echo ($page == 'games' && $pageType == 'mlb')?'active':'';?>">
                                        <a href="<?php echo site_url('games?type=mlb'); ?>"><img src="<?php echo base_url('assets/img/icon_MLB.png')?>">MLB</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <?php } ?>

                    <?php if(in_array($userInfo['user_type'], [0])) { ?>
                    <li class=" has-sub-menu <?php echo ($page == 'picks')?'selected':'';?>">
                        <a href="<?php echo site_url('picks'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-search"></div>
                            </div>
                            <span>Picks</span></a>
                        <div class="sub-menu-w">
                            <div class="sub-menu-header">
                                Picks
                            </div>
                            <div class="sub-menu-icon">
                                <i class="os-icon os-icon-search"></i>
                            </div>
                            <div class="sub-menu-i">
                                <ul class="sub-menu">
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'all_picks')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=all_picks'); ?>">All</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'football')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=football'); ?>"><img src="<?php echo base_url('assets/img/football_icon.png')?>">NFL</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'ncaa_f')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=ncaa_f'); ?>"><img src="<?php echo base_url('assets/img/icon_NFL.png')?>">NCAA F</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'nba')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=nba'); ?>"><img src="<?php echo base_url('assets/img/icon_NBA.png')?>">NBA</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'ncaa_m')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=ncaa_m'); ?>"><img src="<?php echo base_url('assets/img/icon_NCAAM.png')?>">NCAA M</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'soccer')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=soccer'); ?>"><img src="<?php echo base_url('assets/img/icon_soccer.png')?>">Soccer</a>
                                    </li>
                                    <li class="<?php echo ($page == 'picks' && $pageType == 'mlb')?'active':'';?>">
                                        <a href="<?php echo site_url('picks?type=mlb'); ?>"><img src="<?php echo base_url('assets/img/icon_MLB.png')?>">MLB</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class=" has-sub-menu <?php echo ($page == 'worksheets')?'selected':'';?>">
                        <a href="<?php echo site_url('worksheets'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-layers"></div>
                            </div>
                            <span>Worksheets</span></a>
                        <div class="sub-menu-w">
                            <div class="sub-menu-header">
                                Worksheets
                            </div>
                            <div class="sub-menu-icon">
                                <i class="os-icon os-icon-layers"></i>
                            </div>
                            <div class="sub-menu-i">
                                <ul class="sub-menu">
                                    <li class="<?php echo ($page == 'worksheets' && $pageType == 'bet_summary')?'active':'';?>">
                                        <a href="<?php echo site_url('worksheets?type=bet_summary'); ?>">Summary</a>
                                    </li>
                                    <li class="<?php echo ($page == 'worksheets' && $pageType == 'bets')?'active':'';?>">
                                        <a href="<?php echo site_url('worksheets?type=bets'); ?>">Bets</a>
                                    </li>
                                    <li class="<?php echo ($page == 'worksheets' && $pageType == 'bet_sheet')?'active':'';?>">
                                        <a href="<?php echo site_url('worksheets?type=bet_sheet'); ?>">RR and Parlay</a>
                                    </li>
                                    <li class="<?php echo ($page == 'worksheets' && $pageType == 'bets_pick')?'active':'';?>">
                                        <a href="<?php echo site_url('worksheets?type=bets_pick'); ?>">Picks</a>
                                    </li>
                                    <li class="<?php echo ($page == 'worksheets' && $pageType == 'bets_custom')?'active':'';?>">
                                        <a href="<?php echo site_url('worksheets?type=bets_custom'); ?>">Custom</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class=" <?php echo ($page == 'settings')?'selected':'';?>">
                        <a href="<?php echo site_url('settings'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-check-circle"></div>
                            </div>
                            <span>Control</span></a>
                    </li>
                    <li class=" has-sub-menu <?php echo ($page == 'investors')?'selected':'';?>">
                        <a href="<?php echo site_url('investors'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-user"></div>
                            </div>
                            <span>Investors</span></a>
                        <div class="sub-menu-w">
                            <div class="sub-menu-header">
                                Investors
                            </div>
                            <div class="sub-menu-icon">
                                <i class="os-icon os-icon-user"></i>
                            </div>
                            <div class="sub-menu-i">
                                <ul class="sub-menu">
                                    <li class="<?php echo ($page == 'investors' && $pageType == 'investors')?'active':'';?>">
                                        <a href="<?php echo site_url('investors'); ?>">Investor</a>
                                    </li>
                                    <li class="<?php echo ($page == 'investors' && $pageType == 'allocations')?'active':'';?>">
                                        <a href="<?php echo site_url('allocations'); ?>">Money Allocation</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class=" <?php echo ($page == 'sportbooks')?'selected':'';?>">
                        <a href="<?php echo site_url('sportbooks'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-bookmark"></div>
                            </div>
                            <span>SportBooks</span></a>
                    </li>
                    <?php } ?>

                    <?php if(in_array($userInfo['user_type'], [0,1])) { ?>
                    <li class="<?php echo ($page == 'orders')?'selected':'';?>">
                        <a href="<?php echo site_url('orders'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-files"></div>
                            </div>
                            <span>Orders</span></a>
                    </li>
                    <?php } ?>
                    <?php if(in_array($userInfo['user_type'], [0])) { ?>
                    <li class="<?php echo ($page == 'users')?'selected':'';?>">
                        <a href="<?php echo site_url('users'); ?>">
                            <div class="icon-w">
                                <div class="os-icon os-icon-users"></div>
                            </div>
                            <span>Users</span></a>
                    </li>
                    <?php } ?>

                </ul>
                <div class="side-menu-magic">
                    <h4>
          Light Admin
        </h4>
                    <p>
                        Clean Bootstrap 4 Template
                    </p>
                    <div class="btn-w">
                        <a class="btn btn-white btn-rounded" href="https://themeforest.net/item/light-admin-clean-bootstrap-dashboard-html-template/19760124?ref=Osetin" target="_blank">Purchase Now</a>
                    </div>
                </div>
            </div>
            <!--------------------
    END - Main Menu
    -------------------->
            <div class="content-w">
                <div class="content-i">
                    <div class="content-box">
                        <div class="loading-div">
                            <div class="loading-div-inner">
                                <img src="/assets/img/loading-icon.gif">
                            </div>
                        </div>