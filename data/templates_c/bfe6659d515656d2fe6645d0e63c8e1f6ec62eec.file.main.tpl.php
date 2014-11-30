<?php /* Smarty version Smarty-3.1-DEV, created on 2014-11-23 15:56:30
         compiled from "/var/www/columnis-express/public/templates/home/main.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4658622785472566bdd3fd2-45790890%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bfe6659d515656d2fe6645d0e63c8e1f6ec62eec' => 
    array (
      0 => '/var/www/columnis-express/public/templates/home/main.tpl',
      1 => 1416779786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4658622785472566bdd3fd2-45790890',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1-DEV',
  'unifunc' => 'content_5472566be58c07_62883331',
  'variables' => 
  array (
    'prueba' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5472566be58c07_62883331')) {function content_5472566be58c07_62883331($_smarty_tpl) {?><?php echo $_smarty_tpl->getSubTemplate ("../header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>

<?php echo $_smarty_tpl->tpl_vars['prueba']->value;?>

<?php echo $_smarty_tpl->getSubTemplate ("../footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>
<?php }} ?>
