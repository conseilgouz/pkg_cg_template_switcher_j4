<?php
/**
 * @package CG template switcher Module
 * @version 2.1.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2023 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 */
defined('_JEXEC') or die('Restricted access'); 
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

if (empty($templates->options)) { ?>
	<form style="border:none" action="#">
		<div style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
			<?php echo Text::_('NOTEMPLATE'); ?>
		</div>
	</form>
<?php return; } 
$app = Factory::getApplication();
$curr_template = $app->getTemplate(true);  // Current template
$curr_template_idx = Factory::getApplication()->input->cookie->get('cg_template');	 // template ix from cookie 
if (!$curr_template_idx) $curr_template_idx = $curr_template->id;
?>

<form id="cg_ts_form" method="post" style="border:none" >
	<div id="CG_TS_SHOW" style="margin:6px 0 0 0;padding:0;border:none;background:none;overflow:hidden;display:none">
		<div id="CG_TS_Switcher" style="padding:0;border:none;background:none;text-align:center;vertical-align:middle">
		</div>
	</div>
	<div id="CG_TS_CHOICE" style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
		<div id="CG_TS_THUMBNAIL" style="padding:0;border:none;background:none;text-align:center">
			<?php echo HTMLHelper::_('select.genericlist',$templates->options, 'template', "class=\"inputbox\" style=\"margin:0\"", 'value', 'text', $curr_template_idx,'CG_TS_Select'); ?>
		</div>
		<?php if ($params->get("autoswitch","false") == 'false') { // 01.0.14 : autoswitch?>
		<div id="CG_TS_LIST" style="padding:6px 0 0 0;border:none;background:none;text-align:center">
			<input id="CG_TS_OKBtn" class="button" type="button" style="margin:0" value="<?php echo Text::_('CGSELECT'); ?>"/>
			<input id ="CG_TS_CancelBtn" class="button" type="button" style="margin-left:1em" value="<?php echo Text::_('CGCANCEL'); ?>" title="<?php echo Text::_('CGCANCELDESC'); ?>" />
		</div>
		<?php } ?>
	</div>
</form>