<?php /* Smarty version 2.6.30, created on 2020-10-07 19:57:33
         compiled from header.tpl */ ?>
<script src="modules/TS3Admin/webinterface.js" type="text/javascript"></script>
<body onload="onLoad();<?php if ($this->_tpl_vars['liveviewAutoUpdate'] == true): ?>liveViewUpdateInterval = setInterval('serverViewUpdate(false)', 10000);<?php endif; ?>">
<div id="jsMsg" style="display:none;">
	<span id="js_error"><?php echo $this->_tpl_vars['lang']['OGP_LANG_js_error']; ?>
</span>
	<span id="js_ajax_error"><?php echo $this->_tpl_vars['lang']['OGP_LANG_js_ajax_error']; ?>
</span>
	<span id="js_confirm_server_stop" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_confirm_server_stop']; ?>
"></span>
	<span id="js_confirm_server_delete" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_confirm_server_delete']; ?>
"></span>
	<span id="js_notice_server_deleted" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_notice_server_deleted']; ?>
"></span>
	<span id="js_prompt_banduration" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_prompt_banduration']; ?>
"></span>
	<span id="js_prompt_banreason" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_prompt_banreason']; ?>
"></span>
	<span id="js_prompt_msg_to" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_prompt_msg_to']; ?>
"></span>
	<span id="js_prompt_poke_to" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_prompt_poke_to']; ?>
"></span>
	<span id="js_prompt_new_propvalue" title="<?php echo $this->_tpl_vars['lang']['OGP_LANG_js_prompt_new_propvalue']; ?>
"></span>
</div>
<!--[if IE]>
Attention: Internet Explorer is not completely supported.
<![endif]-->