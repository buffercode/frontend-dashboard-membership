<?php
/**
 * Created by Buffercode.
 * User: M A Vinoth Kumar
 */


require_once BC_FED_M_PLUGIN_DIR . '/admin/FED_M_Templates.php';
require_once BC_FED_M_PLUGIN_DIR . '/admin/templates/FED_M_Template1.php';

$membership    = get_option('fed_m_templates');
$temp_selected = isset($membership['selected_template']) ? $membership['selected_template'] : 'Template1';
$temp          = 'FED_Membership\Template\FED_M_'.$temp_selected;
$template      = new $temp();

?>
<div class="row">
    <div class="col-md-12">
        <?php $template->show_template(); ?>
    </div>
</div>
