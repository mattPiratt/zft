<?php /* Smarty version 2.6.19, created on 2008-05-10 17:46:49
         compiled from index.tpl */ ?>
    <h1><?php echo $this->_tpl_vars['title']; ?>
</h1>

    <p><a href="<?php echo $this->_tpl_vars['baseUrl']; ?>
/index/add">Add new album</a></p>
    <table>
        <tr>
            <th>Title</th>
            <th>Artist</th>
            <th>&nbsp;</th>
        </tr>
		<?php $_from = $this->_tpl_vars['albums']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['album']):
?>
        <tr>
            <td><?php echo $this->_tpl_vars['album']->title; ?>
</td>
            <td><?php echo $this->_tpl_vars['album']->artist; ?>
</td>
            <td>
				<a href="<?php echo $this->_tpl_vars['baseUrl']; ?>
/index/edit/id/<?php echo $this->_tpl_vars['album']->id; ?>
">Edit</a>
				<a href="<?php echo $this->_tpl_vars['baseUrl']; ?>
/index/delete/id/<?php echo $this->_tpl_vars['album']->id; ?>
">Delete</a>
			</td>
		</tr>
		<?php endforeach; endif; unset($_from); ?>
    </table>