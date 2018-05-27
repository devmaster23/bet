
<script src='<?php echo base_url('assets/vendor/js/jquery.min.js') ?>'></script>
<script src='<?php echo base_url('assets/vendor/js/popper.min.js') ?>'></script>
<script src='<?php echo base_url('assets/vendor/js/bootstrap.min.js') ?>'></script>
<script src='<?php echo base_url('assets/vendor/js/handsontable.full.min.js') ?>'></script>
<script src='<?php echo base_url('bower_components/select2/dist/js/select2.min.js') ?>'></script>
<script src='<?php echo base_url('assets/DataTables/datatables.min.js') ?>'></script>
<script src='<?php echo base_url('assets/js/common.js') ?>'></script>
<?php
if (isset($scripts)):
	foreach ($scripts as $script_url):
?>
<script src='<?php echo $script_url ?>'></script>
<?php
	endforeach;
endif;
?>
</body>
</html>