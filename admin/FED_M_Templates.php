<?php

namespace FED_Membership;


/**
 * Class FED_M_Templates
 *
 * @package FED_Membership
 */
class FED_M_Templates
{

    public $settings;

    public function __construct()
    {
        add_action('wp_ajax_fed_m_save_membership', array($this, 'save_membership'));
        $this->settings = $this->get_settings_value();
    }

    /**
     * @return array|mixed
     */
    public function get_settings_value()
    {
        $options = get_option('fed_m_templates');
        if ($options && ! empty($options)) {
            return ($options);
        }

        return array();
    }

    /**
     * Create Layouts
     */
    public function create_layouts()
    {
        $temp_selected = isset($this->settings['selected_template']) ? $this->settings['selected_template'] : 'Template1';
        $temp          = 'FED_Membership\Template\FED_M_'.$temp_selected;
        $template      = new $temp;
        ?>
        <div class="text-right padd_bot_10">
            <a href="<?php menu_page_url('fed_membership_menu') ?>" class="btn btn-secondary">
                <i class="fa fa-undo" aria-hidden="true"></i>
                Back to Membership Settings
            </a>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Please select one Membership Template</h3>
            </div>
            <div class="panel-body">
                <div class="row template1">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <?php $template->template() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function layout_content()
    {
        $temp_selected = $this->settings && isset($this->settings['selected_template']) ? $this->settings['selected_template'] : 'Template1';
        $class         = 'FED_Membership\Template\FED_M_'.$temp_selected;
        $template      = new $class();
        echo fed_loader('', 'Please wait it may take some time to fetch from PayPal');
        ?>
        <div class="text-right padd_bot_10">
            <a href="<?php menu_page_url('fed_membership_menu') ?>" class="btn btn-secondary">
                <i class="fa fa-undo" aria-hidden="true"></i>
                Back to Membership Settings
            </a>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Add Content to Template</h3>
            </div>
            <div class="panel-body">
                <div class="row fed_m_template_container template1">
                    <div class="col-md-12">
                        <div class="row fed_m_templates">
                            <form method="post" class="fed_ajax"
                                  action="<?php echo admin_url('admin-ajax.php?action=fed_m_save_membership&fed_nonce='.wp_create_nonce('fed_nonce')); ?>">
                                <div class="col-md-12">
                                    <button class="btn btn-secondary pull-right btn-lg">
                                        <i class="fa fa-save" aria-hidden="true"></i>
                                        Save
                                    </button>
                                </div>
                                <div class="fed_m_content_container">
                                    <?php echo $template->add_content() ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save
     */
    public function save_membership()
    {
        $request = $_REQUEST;
        fed_verify_nonce($request);
        $value         = array();
        $template_name = isset($this->settings['selected_template']) ? $this->settings['selected_template'] : 'Template1';
        foreach ($request[$template_name] as $index => $content) {
            $type = isset($content['payments']['type']) && null !== $content['payments']['type'] ? fed_sanitize_text_field($content['payments']['type']) : null;

            $type_value_subscription = $url_id = isset($content['payments']['type_value_subscription']) && null !== $content['payments']['type_value_subscription'] ? fed_sanitize_text_field($content['payments']['type_value_subscription']) : null;

            $type_value_one_time = isset($content['payments']['type_value_one_time']) && null !== $content['payments']['type_value_one_time'] ? fed_sanitize_text_field($content['payments']['type_value_one_time']) : null;

            if($type === 'one_time'){
                $url_id = $type_value_one_time;
            }

            $value[$index] = array(
                    'header'     => array(
                            'heading'     => array(
                                    'title'      => isset($content['header']['heading']['title']) && null !== $content['header']['heading']['title'] ? fed_sanitize_text_field($content['header']['heading']['title']) : 'Template 1 ',
                                    'bg_color'   => isset($content['header']['heading']['bg_color']) && null !== $content['header']['heading']['bg_color'] ? fed_sanitize_text_field($content['header']['heading']['bg_color']) : null,
                                    'font_color' => isset($content['header']['heading']['font_color']) && null !== $content['header']['heading']['font_color'] ? fed_sanitize_text_field($content['header']['heading']['font_color']) : null,
                                    'font_size'  => isset($content['header']['heading']['font_size']) && null !== $content['header']['heading']['font_size'] ? fed_sanitize_text_field($content['header']['heading']['font_size']) : null,
                            ),
                            'sub_title'   => array(
                                    'title'      => isset($content['header']['sub_title']['title']) && null !== $content['header']['sub_title']['title'] ? fed_sanitize_text_field($content['header']['sub_title']['title']) : 'Lorem ipsum dolor sit amet',
                                    'bg_color'   => isset($content['header']['sub_title']['bg_color']) && null !== $content['header']['sub_title']['bg_color'] ? fed_sanitize_text_field($content['header']['sub_title']['bg_color']) : null,
                                    'font_color' => isset($content['header']['sub_title']['font_color']) && null !== $content['header']['sub_title']['font_color'] ? fed_sanitize_text_field($content['header']['sub_title']['font_color']) : null,
                                    'font_size'  => isset($content['header']['sub_title']['font_size']) && null !== $content['header']['sub_title']['font_size'] ? fed_sanitize_text_field($content['header']['sub_title']['font_size']) : null,
                            ),
                            'price_value' => array(
                                    'title'      => isset($content['header']['price_value']['title']) && null !== $content['header']['price_value']['title'] ? fed_sanitize_text_field($content['header']['price_value']['title']) : '10',
                                    'bg_color'   => isset($content['header']['price_value']['bg_color']) && null !== $content['header']['price_value']['bg_color'] ? fed_sanitize_text_field($content['header']['price_value']['bg_color']) : null,
                                    'font_color' => isset($content['header']['price_value']['font_color']) && null !== $content['header']['price_value']['font_color'] ? fed_sanitize_text_field($content['header']['price_value']['font_color']) : null,
                                    'font_size'  => isset($content['header']['price_value']['font_size']) && null !== $content['header']['price_value']['font_size'] ? fed_sanitize_text_field($content['header']['price_value']['font_size']) : null,
                            ),
                            'currency'    => array(
                                    'title'      => isset($content['header']['currency']['title']) && null !== $content['header']['currency']['title'] ? fed_sanitize_text_field($content['header']['currency']['title']) : '$',
                                    'bg_color'   => isset($content['header']['currency']['bg_color']) && null !== $content['header']['currency']['bg_color'] ? fed_sanitize_text_field($content['header']['currency']['bg_color']) : null,
                                    'font_color' => isset($content['header']['currency']['font_color']) && null !== $content['header']['currency']['font_color'] ? fed_sanitize_text_field($content['header']['currency']['font_color']) : null,
                                    'font_size'  => isset($content['header']['currency']['font_size']) && null !== $content['header']['currency']['font_size'] ? fed_sanitize_text_field($content['header']['currency']['font_size']) : null,
                            ),
                            'month'       => array(
                                    'title'      => isset($content['header']['month']['title']) && null !== $content['header']['month']['title'] ? fed_sanitize_text_field($content['header']['month']['title']) : '/mo',
                                    'bg_color'   => isset($content['header']['month']['bg_color']) && null !== $content['header']['month']['bg_color'] ? fed_sanitize_text_field($content['header']['month']['bg_color']) : null,
                                    'font_color' => isset($content['header']['month']['font_color']) && null !== $content['header']['month']['font_color'] ? fed_sanitize_text_field($content['header']['month']['font_color']) : null,
                                    'font_size'  => isset($content['header']['month']['font_size']) && null !== $content['header']['month']['font_size'] ? fed_sanitize_text_field($content['header']['month']['font_size']) : null,
                            ),
                            'bg_color'    => isset($content['header']['bg_color']) && null !== $content['header']['bg_color'] ? fed_sanitize_text_field($content['header']['bg_color']) : null,
                            'font_color'  => isset($content['header']['font_color']) && null !== $content['header']['font_color'] ? fed_sanitize_text_field($content['header']['font_color']) : null,
                            'font_size'   => isset($content['header']['font_size']) && null !== $content['header']['font_size'] ? fed_sanitize_text_field($content['header']['font_size']) : '72',
                    ),
                    'body'       => array(
                            'bg_color'   => isset($content['body']['bg_color']) && null !== $content['body']['bg_color'] ? fed_sanitize_text_field($content['body']['bg_color']) : null,
                            'font_color' => isset($content['body']['font_color']) && null !== $content['body']['font_color'] ? fed_sanitize_text_field($content['body']['font_color']) : null,
                            'font_size'  => isset($content['body']['font_size']) && null !== $content['body']['font_size'] ? fed_sanitize_text_field($content['body']['font_size']) : null,
                    ),
                    'footer'     => array(
                            'content'    => array(
                                    'title'      => isset($content['footer']['content']['title']) && null !== $content['footer']['content']['title'] ? fed_sanitize_text_field($content['footer']['content']['title']) : 'Sign Up',
                                    'bg_color'   => isset($content['footer']['content']['bg_color']) && null !== $content['footer']['content']['bg_color'] ? fed_sanitize_text_field($content['footer']['content']['bg_color']) : null,
                                    'font_color' => isset($content['footer']['content']['font_color']) && null !== $content['footer']['content']['font_color'] ? fed_sanitize_text_field($content['footer']['content']['font_color']) : null,
                                    'font_size'  => isset($content['footer']['content']['font_size']) && null !== $content['footer']['content']['font_size'] ? fed_sanitize_text_field($content['footer']['content']['font_size']) : null,
                            ),
                            'bg_color'   => isset($content['footer']['bg_color']) && null !== $content['footer']['bg_color'] ? fed_sanitize_text_field($content['footer']['bg_color']) : null,
                            'font_color' => isset($content['footer']['font_color']) && null !== $content['footer']['font_color'] ? fed_sanitize_text_field($content['footer']['font_color']) : null,
                            'font_size'  => isset($content['footer']['font_size']) && null !== $content['footer']['font_size'] ? fed_sanitize_text_field($content['footer']['font_size']) : null,
                    ),
                    'extra'      => array(
                            'border' => array(
                                    'color' => isset($content['extra']['border']['color']) && null !== $content['extra']['border']['color'] ? fed_sanitize_text_field($content['extra']['border']['color']) : null,
                                    'size'  => isset($content['extra']['border']['size']) && null !== $content['extra']['border']['size'] ? fed_sanitize_text_field($content['extra']['border']['size']) : null,
                                    'type'  => isset($content['extra']['border']['type']) && null !== $content['extra']['border']['type'] ? fed_sanitize_text_field($content['extra']['border']['type']) : null,
                            ),
                            'url'    => admin_url('admin-ajax.php?action=fed_m_payment_redirect&type='.$type.'&id='.$url_id),
                    ),
                    'payments'   => array(
                            'type'                    => $type,
                            'roles'                   => isset($content['payments']['roles']) && null !== $content['payments']['roles'] ? fed_sanitize_text_field($content['payments']['roles']) : null,
                            'type_value_subscription' => $type_value_subscription,
                            'type_value_one_time'     => $type_value_one_time,
                    ),
                    'bg_color'   => isset($content['bg_color']) && null !== $content['bg_color'] ? fed_sanitize_text_field($content['bg_color']) : null,
                    'font_color' => isset($content['font_color']) && null !== $content['font_color'] ? fed_sanitize_text_field($content['font_color']) : null,
                    'font_size'  => isset($content['font_size']) && null !== $content['font_size'] ? fed_sanitize_text_field($content['font_size']) : null,
            );

            foreach ($content['body']['content'] as $key => $item) {
                $value[$index]['body']['content'][$key]['title'] = $item['title'];
            }

        }

        $temp_value = $this->save_template($value);

        update_option('fed_m_templates', $temp_value);

        wp_send_json_success(['message' => 'Successfully Updated']);

    }

    /**
     * @param       $template
     * @param array $value
     *
     * @return array
     */
    public function save_template($value = array())
    {
        $template = isset($this->settings['selected_template']) ? $this->settings['selected_template'] : 'Template1';

        $this->settings['template'][$template]['value'] = $value;

        return $this->settings;
    }


}