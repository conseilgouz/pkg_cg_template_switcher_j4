<?php
/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz
 *
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Component\Fields\Administrator\Model\FieldModel;
use ConseilGouz\Module\CGTemplateSwitcher\Site\Helper\CGTemplateSwitcherHelper;

$app = Factory::getApplication();
if (!PluginHelper::isEnabled('system', 'cgstyle')) {
    $app->enqueueMessage(Text::_('CG_ACTIVATE'), 'error');
    return false;
}
$document 		= $app->getDocument();
$modulefield	= 'media/mod_cg_template_switcher/';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();

if ($params->get('css')) {
    $wa->addInlineStyle($params->get('css'));
}

$templates =  CGTemplateSwitcherHelper::getTemplates($params);

$templates_js = array();
if ($params->get('showpreview', 'true') == 'true') {
    foreach ($templates->images as $template => $image) {
        $arr['width'] =  $image->width;
        $arr['height'] = $image->height;
        $arr['src'] = $image->src;
        $arr['preview'] = $image->preview;
        $templates_js[$template] = $arr;
    }
}
$document->addScriptOptions(
    'mod_cg_template_switcher',
    array('cookie_duration' => $params->get('cookie_duration', 0),'showpreview' => $params->get('showpreview', 'true'),
          'autoswitch' => $params->get('autoswitch', 'false'),
          'noimage' => Text::_('NOIMAGE'),'templates' => $templates_js)
);
if ((bool)$app->getConfig()->get('debug')) { // Mode debug
    $document->addScript(''.URI::base(true).'/media/mod_cg_template_switcher/js/init.js');
} else {
    $wa->registerAndUseScript('cgtemplateswitcher'.$module->id, $modulefield.'js/init.js');
}

if (empty($templates->options)) { ?>
	<form style="border:none" action="#">
		<div style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
			<?php echo Text::_('NOTEMPLATE'); ?>
		</div>
	</form>
<?php return;
}
$curr_template = $app->getTemplate(true);  // Current template
$curr_template_idx = $app->input->cookie->get('cg_template');	 // template ix from cookie
if (!$curr_template_idx) {
    $curr_template_idx = $curr_template->id;
}
$user = $app->getIdentity();
if ($user->id) {
    $test = FieldsHelper::getFields('com_users.user', $user);
    $template_id = 0;
    foreach ($test as $field) {
        if ($field->type == 'cgtemplateswitcher') {
            $template_id = $field->value;
            $field_id = $field->id;
        }
    }
    if (($template_id) && ($template_id != $curr_template_idx)) {
        // need to update template switcher field value
        $fieldmodel = new FieldModel(array('ignore_request' => true));
        $fieldmodel->setFieldValue($field_id, $user->id, $curr_template_idx);
    }
}
?>

<form id="cg_ts_form" method="post" style="border:none" >
	<div id="CG_TS_SHOW" style="margin:6px 0 0 0;padding:0;border:none;background:none;overflow:hidden;display:none">
		<div id="CG_TS_Switcher" style="padding:0;border:none;background:none;text-align:center;vertical-align:middle">
		</div>
	</div>
	<div id="CG_TS_CHOICE" style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
		<div id="CG_TS_THUMBNAIL" style="padding:0;border:none;background:none;text-align:center">
			<?php echo HTMLHelper::_('select.genericlist', $templates->options, 'template', "class=\"inputbox\" style=\"margin:0\"", 'value', 'text', $curr_template_idx, 'CG_TS_Select'); ?>
		</div>
		<?php if ($params->get("autoswitch", "false") == 'false') { // 01.0.14 : autoswitch?>
		<div id="CG_TS_LIST" style="padding:6px 0 0 0;border:none;background:none;text-align:center">
			<input id="CG_TS_OKBtn" class="button" type="button" style="margin:0" value="<?php echo Text::_('CGSELECT'); ?>"/>
			<input id ="CG_TS_CancelBtn" class="button" type="button" style="margin-left:1em" value="<?php echo Text::_('CGCANCEL'); ?>" title="<?php echo Text::_('CGCANCELDESC'); ?>" />
		</div>
		<?php } ?>
	</div>
</form>