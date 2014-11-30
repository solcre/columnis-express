<?php /* Smarty version Smarty-3.1-DEV, created on 2014-11-23 15:31:17
         compiled from "/var/www/columnis-express/module/Columnis/view/columnis/page/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17072743154723f759382f1-93499405%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '14681582f7d0abf0e03ea00e0dc9c9d7e953368d' => 
    array (
      0 => '/var/www/columnis-express/module/Columnis/view/columnis/page/index.tpl',
      1 => 1416778273,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17072743154723f759382f1-93499405',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_54723f759ad6c1_23244294',
  'variables' => 
  array (
    'prueba' => 0,
    'var' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54723f759ad6c1_23244294')) {function content_54723f759ad6c1_23244294($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("../../../../../public/templates/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php $_smarty_tpl->tpl_vars['var'] = new Smarty_Variable;$_smarty_tpl->tpl_vars['var']->step = 1;$_smarty_tpl->tpl_vars['var']->total = (int) ceil(($_smarty_tpl->tpl_vars['var']->step > 0 ? 10+1 - (0) : 0-(10)+1)/abs($_smarty_tpl->tpl_vars['var']->step));
if ($_smarty_tpl->tpl_vars['var']->total > 0) {
for ($_smarty_tpl->tpl_vars['var']->value = 0, $_smarty_tpl->tpl_vars['var']->iteration = 1;$_smarty_tpl->tpl_vars['var']->iteration <= $_smarty_tpl->tpl_vars['var']->total;$_smarty_tpl->tpl_vars['var']->value += $_smarty_tpl->tpl_vars['var']->step, $_smarty_tpl->tpl_vars['var']->iteration++) {
$_smarty_tpl->tpl_vars['var']->first = $_smarty_tpl->tpl_vars['var']->iteration == 1;$_smarty_tpl->tpl_vars['var']->last = $_smarty_tpl->tpl_vars['var']->iteration == $_smarty_tpl->tpl_vars['var']->total;?>
Prueba: <?php echo $_smarty_tpl->tpl_vars['prueba']->value;?>
-<?php echo $_smarty_tpl->tpl_vars['var']->value;?>
    
<?php }} ?><?php }} ?>
