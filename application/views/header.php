<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>BetGame - <?php echo $page; ?></title>
	<link href='<?php echo base_url('assets/vendor/css/bootstrap.min.css') ?>' rel='stylesheet' />
  <link href='<?php echo base_url('assets/vendor/css/handsontable.full.min.css') ?>' rel='stylesheet' />
  <link href='<?php echo base_url('bower_components/select2/dist/css/select2.min.css') ?>' rel='stylesheet' />
  <link href='<?php echo base_url('assets/vendor/css/handsontable.full.min.css') ?>' rel='stylesheet' />
  <link href='<?php echo base_url('assets/css/style.css') ?>' rel='stylesheet' />
</head>
<body>

<section class="header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">Bet Game</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item <?php echo ($page == 'games')?'active':'';?>">
            <a class="nav-link" href="<?php echo site_url('games'); ?>">Games <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item <?php echo ($page == 'picks')?'active':'';?>">
            <a class="nav-link" href="<?php echo site_url('picks'); ?>">Picks</a>
          </li>
          <li class="nav-item <?php echo ($page == 'worksheets')?'active':'';?>">
            <a class="nav-link" href="<?php echo site_url('worksheets'); ?>">Worksheets</a>
          </li>
        </ul>
      </div>
    </nav>
</section>