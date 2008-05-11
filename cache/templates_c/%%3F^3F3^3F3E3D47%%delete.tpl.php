<?php /* Smarty version 2.6.19, created on 2008-05-10 18:00:53
         compiled from index/delete.tpl */ ?>
<h1><?php echo $this->_tpl_vars['title']; ?>
</h1>

<?php if ($this->_tpl_vars['album']->id > 0): ?>
<form action="<?php echo $this->_tpl_vars['baseUrl']; ?>
/index/delete" method="post">
	<p>Are you sure that you want to delete "<?php echo $this->_tpl_vars['album']->title; ?>
" by
		"<?php echo $this->_tpl_vars['album']->artist; ?>
" ?</p>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['album']->id; ?>
" />
		<input type="submit" name="del" value="Yes" />
		<input type="submit" name="del" value="No" />
	</div>
</form>
<?php else: ?>
<p>Cannot find album.</p>
<?php endif; ?>