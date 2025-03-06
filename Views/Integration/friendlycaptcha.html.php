<?php

$js = $view['assets']->getUrl('plugins/MauticFriendlyCaptchaBundle/Views/Public/js/add-captcha.js', null, null, true);
$fcWidgetJs = $view['assets']->getUrl('plugins/MauticFriendlyCaptchaBundle/Views/Public/js/widget.js', null, null, true);
$fcWidgetModuleJs = $view['assets']->getUrl('plugins/MauticFriendlyCaptchaBundle/Views/Public/js/widget.module.min.js', null, null, true);
$siteKey   = $field['customParameters']['site_key'];

$containerType     = 'div-wrapper';

include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$action   = $app->getRequest()->get('objectAction');
$settings = $field['properties'];

$formName    = str_replace('_', '', $formName);

preg_match('/id="([^"]+)"/', $containerAttr, $matches);
$wrapperId = $matches[1];

preg_match('/name="([^"]+)"/', $inputAttr, $matches);
$inputName = $matches[1];

$hashedFormName = md5($formName);
$formButtons = (!empty($inForm)) ? $view->render(
    'MauticFormBundle:Builder:actions.html.php',
    [
        'deleted'        => false,
        'id'             => $id,
        'formId'         => $formId,
        'formName'       => $formName,
        'disallowDelete' => false,
    ]
) : '';

if($field['customParameters']['version'] == 'v1') {
    $jsElement .= <<<JSELEMENT
<script
  type="module"
  src="$fcWidgetModuleJs"
  async
  defer
></script>
<script nomodule src="$fcWidgetJs" async defer></script>
JSELEMENT;
} else {
    $jsElement = "<script>not implemented</script>";
}

// Ensure necessary variables are defined to prevent errors
$inBuilder = $inBuilder ?? false;
$jsElement = $jsElement ?? '';

// Only add JavaScript when NOT in the form builder
if (!$inBuilder) {
    $html = <<<HTML
        {$jsElement}
        <script src="{$js}"></script>
        <script type="text/javascript">
          window.setTimeout(function() {
                scheduleAddCaptcha('{$wrapperId}', '{$inputName}', '{$siteKey}');
          }, 2000);
        </script>
        <div $containerAttr>
HTML;
} else {
    $html = <<<HTML
        <div $containerAttr>
HTML;
}



$html .= <<<HTML
        <span class="mauticform-errormsg" style="display: none;"></span>
    </div>
HTML;
?>



<?php
echo $html;
?>

