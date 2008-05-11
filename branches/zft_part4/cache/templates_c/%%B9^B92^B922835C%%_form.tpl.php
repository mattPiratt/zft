<?php /* Smarty version 2.6.19, created on 2008-05-10 18:00:37
         compiled from index/_form.tpl */ ?>
    <form action="<?php echo $this->_tpl_vars['baseUrl']; ?>
/index/<?php echo $this->_tpl_vars['action']; ?>
" method="post">
        <div>
            <label for="artist">Artist</label>
            <input type="text" name="artist" value="<?php echo $this->_tpl_vars['album']->artist; ?>
"/>
        </div>
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" value="<?php echo $this->_tpl_vars['album']->title; ?>
"/>
        </div>
        <div id="formbutton">
            <input type="hidden" name="id" value="<?php echo $this->_tpl_vars['album']->id; ?>
" />
            <input type="submit" name="add" value="<?php echo $this->_tpl_vars['buttonText']; ?>
" />
        </div>
    </form>