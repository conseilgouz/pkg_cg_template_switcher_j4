<?php
/**
 * @package CG template switcher Module
 * @version 2.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 */
defined('_JEXEC') or die('Restricted access'); ?>
<?php 
if (empty($templates->options)) { ?>
	<form style="border:none" action="#">
		<div style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
			<?php echo JText::_('NOTEMPLATE'); ?>
		</div>
	</form>
<?php return; } 
$app = JFactory::getApplication();
$curr_template = $app->getTemplate();  // Current template
$curr_template_idx = JFactory::getApplication()->input->cookie->get('cg_template');	 // template ix from cookie 
// look for home template
$home_template_id = 0;
foreach ($templates->home as $key => $value) {
	if ($value == 1) {
		$home_template_id = $key;
	}
}
if (($curr_template_idx == 0) && ($home_template_id > 0)) $curr_template_idx = $home_template_id;
?>

<form id="cg_ts_form" method="post" style="border:none" >
	<div id="CG_TS_SHOW" style="margin:6px 0 0 0;padding:0;border:none;background:none;overflow:hidden;display:none">
		<div id="CG_TS_Switcher" style="padding:0;border:none;background:none;text-align:center;vertical-align:middle">
		</div>
	</div>
	<div id="CG_TS_CHOICE" style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
		<div id="CG_TS_THUMBNAIL" style="padding:0;border:none;background:none;text-align:center">
			<?php echo JHtmlSelect::genericlist($templates->options, 'template', "class=\"inputbox\" style=\"margin:0\"", 'value', 'text', $curr_template_idx,'CG_TS_Select'); ?>
		</div>
		<?php if ($params->get("autoswitch","false") == 'false') { // 01.0.14 : autoswitch?>
		<div id="CG_TS_LIST" style="padding:6px 0 0 0;border:none;background:none;text-align:center">
			<input id="CG_TS_OKBtn" class="button" type="button" style="margin:0" value="<?php echo JText::_('CGSELECT'); ?>"/>
			<input id ="CG_TS_CancelBtn" class="button" type="button" style="margin-left:1em" value="<?php echo JText::_('CGCANCEL'); ?>" title="<?php echo JText::_('CGCANCELDESC'); ?>" />
		</div>
		<?php } ?>
	</div>
</form>