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
$user = $app->getIdentity();
$modulefield	= 'media/mod_cg_template_switcher/';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();

$wa->registerAndUseStyle('cgtemplateswitcher'.$module->id, $modulefield.'css/cgtemplateswitcher.css');

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
$auto = $params->get('autoswitch', 'false');

$document->addScriptOptions(
    'mod_cg_template_switcher_'.$module->id,
    array('id' => $module->id,
          'cookie_duration' => $params->get('cookie_duration', 0),'showpreview' => $params->get('showpreview', 'true'),
          'autoswitch' => $auto,
          'noimage' => Text::_('NOIMAGE'),'templates' => $templates_js,
          'userfield' => $params->get('user_field', 'false'),
          'grayscale' => $params->get('grayscale', '80')
          )
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

$cookieValue = $app->input->cookie->getRaw('cg_template', ':');	 // template ix/color mode from cookie
$cookie = explode(':', $cookieValue);
$curr_template_idx = $cookie[0];
$color = 0;
$color_img = "sun.svg";
if (sizeof($cookie) == 2) {
    $color = $cookie[1];
    if ($color > 0) {
        $color_img = "moon-stars.svg";
    }
}

if (!$curr_template_idx) {
    $curr_template_idx = $curr_template->id;
}

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

<form id="cg_ts_form_<?php echo $module->id;?>" class="cg_ts_form" data="<?php echo $module->id;?>" method="post" style="border:none" >
<?php if ($params->get('templatesall') != 'none') { ?>
    <div id="CG_TS_SHOW_<?php echo $module->id;?>" class="CG_TS_SHOW" style="margin:6px 0 0 0;padding:0;border:none;background:none;overflow:hidden;display:none">
		<div id="CG_TS_Switcher_<?php echo $module->id;?>" style="padding:0;border:none;background:none;text-align:center;vertical-align:middle">
		</div>
	</div>
	<div id="CG_TS_CHOICE_<?php echo $module->id;?>" style="margin:6px 0;padding:0;border:none;background:none;overflow:hidden">
		<div id="CG_TS_THUMBNAIL_<?php echo $module->id;?>" data="<?php echo $module->id;?>" style="padding:0;border:none;background:none;text-align:center">
			<?php echo HTMLHelper::_('select.genericlist', $templates->options, 'template', "class=\"inputbox CG_TS_Select\" style=\"margin:0\"", 'value', 'text', $curr_template_idx, 'CG_TS_Select_'.$module->id); ?>
		</div>
		<?php if ($params->get("autoswitch", "false") == 'false') { // 01.0.14 : autoswitch?>
		<div id="CG_TS_LIST_<?php echo $module->id;?>" style="padding:6px 0 0 0;border:none;background:none;text-align:center">
			<input id="CG_TS_OKBtn_<?php echo $module->id;?>" data="<?php echo $module->id;?>" class="button CG_TS_OKBtn" type="button" style="margin:0" value="<?php echo Text::_('CGSELECT'); ?>"/>
			<input id ="CG_TS_CancelBtn_<?php echo $module->id;?>" data="<?php echo $module->id;?>" class="button CG_TS_CancelBtn" type="button" style="margin-left:1em" value="<?php echo Text::_('CGCANCEL'); ?>" title="<?php echo Text::_('CGCANCELDESC'); ?>" />
		</div>
		<?php } ?>
	</div>
    <?php } ?>
    <?php if ($params->get('showcolor', 'false') == 'true') { ?>    
    <div id="CG_COLOR_<?php echo $module->id;?>" style="text-align:center;min-width:4em">
        <input type="button" class="button CG_COLOR_BTN" data="<?php echo $module->id;?>" 
             id="cg_color_btn_<?php echo $module->id;?>"  title="<?php echo Text::_('CGSWITCHCOLOR'); ?>"
             style="width:3em;background-image : url('<?php echo URI::base(true).'/'.$modulefield;?>icons/<?php echo $color_img;?>');background-position: center;background-size:100% 100%;" >
    </div>
    <?php } ?>
</form>
