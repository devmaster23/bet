<?php
	$page = 'games';
	$scripts = [
		base_url('assets/js/games.sheet.js'),
	];
	$this->load->view('header', array('page' => $page)) ?>
<script>
	var betweek = <?php echo $betweek ?>;
	var api_url = "<?php echo site_url('games'); ?>";
	var pageType = "<?php echo $pageType; ?>";
	var pageTitle = "<?php echo $pageTitle; ?>";
</script>
<div class="container page-title">
	<h1 class="text-center" id="pageTitle"></h1>
</div>

<div id="main">
	<div class="header-div">
		<div class="game-title-wrapper">
			<span class="game-title">
				<?php if(!is_null($pageTitleIcon)) { ?>
        	<img src="<?php echo base_url('assets/img/'.$pageTitleIcon)?>">
        <?php }?>
				<?=$pageTitle?>
			</span>
			<?php $this->load->view('partials/game-select', array('betweek' => $betweek)); ?>
		</div>
		<div class="save-button-div">
			<button type="button" class="btn btn-success btn-lg enter-game_save-button" onClick="updateTable()">Update</button>
		</div>
	</div>
	<div class="content-div">
		<div class="enter-game">
			<div class="element-box no-border-radius gray-top">
				<div class="table-wrapper" id="<?php echo $pageType?>">
					<div class="sheet"></div>
				</div>
			</div>
		</div>
	</div>	
</div>
<?php $this->load->view('footer', array('scripts' => $scripts)) ?>