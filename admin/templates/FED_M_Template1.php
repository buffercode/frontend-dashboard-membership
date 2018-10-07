<?php
namespace FED_Membership\Template;


use FED_Membership\FED_M_Templates;

/**
 * Class FED_M_Template1
 *
 * @package FED_Membership\Template
 */
class FED_M_Template1 extends FED_M_Templates
{
    const TEMPLATE = 'Template1';

    public $template;

    public function __construct()
    {
        parent::__construct();
        $this->template = $this->get_template();
        add_action('wp_ajax_fed_m_add_template', array($this, 'add_template'));
        add_action('wp_ajax_fed_m_add_template_content', array($this, 'add_template_content'));
    }

    /**
     * Get Template
     *
     * @return mixed
     */
    public function get_template()
    {
        $template = array(
                'header'     => array(
                        'heading'     => array(
                                'title'      => 'Template 1',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'sub_title'   => array(
                                'title'      => 'Lorem ipsum dolor sit amet',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'price_value' => array(
                                'title'      => '10',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'currency'    => array(
                                'title'      => '$',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'month'       => array(
                                'title'      => '/mo',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'bg_color'    => '#0AAAAA',
                        'font_color'  => '#ffffff',
                        'font_size'   => '14',
                ),
                'body'       => array(
                        'content'    => array(
                                array(
                                        'title'      => '50GB Disk Space',
                                        'bg_color'   => null,
                                        'font_color' => null,
                                        'font_size'  => null,
                                ),
                                array(
                                        'title'      => '50 Email Accounts',
                                        'bg_color'   => null,
                                        'font_color' => null,
                                        'font_size'  => null,
                                ),
                                array(
                                        'title'      => '50GB Monthly Bandwidth',
                                        'bg_color'   => null,
                                        'font_color' => null,
                                        'font_size'  => null,
                                ),
                                array(
                                        'title'      => '10 Subdomains',
                                        'bg_color'   => null,
                                        'font_color' => null,
                                        'font_size'  => null,
                                ),
                                array(
                                        'title'      => '15 Domains',
                                        'bg_color'   => null,
                                        'font_color' => null,
                                        'font_size'  => null,
                                ),
                        ),
                        'bg_color'   => null,
                        'font_color' => null,
                        'font_size'  => null,
                ),
                'footer'     => array(
                        'content'    => array(
                                'title'      => 'sign up',
                                'bg_color'   => null,
                                'font_color' => null,
                                'font_size'  => null,
                        ),
                        'bg_color'   => null,
                        'font_color' => null,
                        'font_size'  => null,
                ),
                'extra'      => array(
                        'border' => array(
                                'color' => null,
                                'size'  => null,
                                'type'  => null,
                        ),
                ),
                'bg_color'   => null,
                'font_color' => null,
                'font_size'  => null,
        );

        return apply_filters('fed_m_template', $template);
    }

    public function add_template_content()
    {
        $request = fed_sanitize_text_field($_REQUEST);
        $html   = '';
        $random = fed_get_random_string(4);
        if ( ! isset($request['random_key']) || empty($request['random_key'])) {
            wp_send_json_error(array('message' => 'Something went wrong, please reload the page and try'));
        }

        $html .= '<li class="fed_m_single_pricing_container fed_m_hover">
                                <div class="fed_flex_start_center">
                                    <div><input type="text" class="form-control"
                                                name="Template1['.$request['random_key'].'][body][content]['.$random.'][title]"
                                                value="" placeholder="Text"/></div>
                                    <div class="fed_mouser_pointer">
                                        <i class="fa fa-plus fed_m_add_content" aria-hidden="true"></i>
                                        <i class="fa fa-trash fed_m_remove_content" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>';

        return wp_send_json_success(array('html' => $html));
    }

    public function add_template()
    {
        wp_send_json_success(['html' => $this->add_single_template()]);
    }

    /**
     * @return string
     */
    public function add_single_template()
    {
        $html                 = '';
        $templates            = $this->get_template_values(true);
        $subscription_payment = fed_m_get_subscription_payment();
//        var_dump($this->settings );
        foreach ($templates as $template) {
            $random            = fed_get_random_string(5);
            $color             = '#0AAAAA';
            $border_color      = '#0AAAAA';
            $border_left_color = '#0AAAAA';
            if (isset($template['extra']['border']['color']) && null !== $template['extra']['border']['color']) {
                $color             = $template['extra']['border']['color'];
                $border_color      = 'border-color:'.$template['extra']['border']['color'].';';
                $border_left_color = 'border-color:'.$template['extra']['border']['color'].';';
            }

            $border_size = isset($template['extra']['border']['size']) && null !== $template['extra']['border']['size'] ? $template['extra']['border']['size'].'px' : '2px';

            $border_type = isset($template['extra']['border']['type']) && null !== $template['extra']['border']['type'] ? $template['extra']['border']['type'] : 'solid';
            $border      = $border_size.' '.$border_type.' '.$color;

            $payment_type_one = isset($template['payments']['type']) && $template['payments']['type'] === 'single' ? 'checked' : '';
            $payment_type_sub = isset($template['payments']['type']) && $template['payments']['type'] === 'subscription' ? 'checked' : '';


            $html .= '<div class="fed_m_template">';
            $html .= '
            <style>
                .bc_fed .template1 .pricingTable .read:hover {
                '.$border_color.';color:'.$color.';
                }

                .bc_fed .template1 .pricingTable .read:hover:before,
                .bc_fed .template1 .pricingTable .read:hover:after {
                '.$border_left_color.';
                }

                .bc_fed .template1 .pricingTable:after {
                    border-bottom: '.$border.';
                    border-top: '.$border.';
                    transform: scaleX(0);
                    transform-origin: 0 100% 0;
                }

                .bc_fed .template1 .pricingTable:before {
                    border-right: '.$border.';
                    border-left: '.$border.';
                    transform: scaleY(0);
                    transform-origin: 100% 0 0;
                }
            </style>';

            $html .= '<div class="pull-right padd_top_20">
                    <button class="btn btn-primary fed_m_add_membership" data-url="'.admin_url('admin-ajax.php?action=fed_m_add_template&fed_nonce='.wp_create_nonce("fed_nonce")).'">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>

                    <button class="btn btn-danger fed_m_remove_membership">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="clearfix"></div>';

            $html .= '<div class="pricingTable initial fed_m_hover" '.$this->get_style($template).'>
                    <div class="pricingTable-header fed_m_hover" '.$this->get_style($template['header']['heading']).'>
                        <div class="heading fed_m_hover" '.$this->get_style($template['header']['heading']).'>
                            <input '.$this->get_style($template['header']['heading']).' class="form-control fed_m_hover" placeholder="Title" value=" '.$template['header']['heading']['title'].' " name="Template1['.$random.'][header][heading][title]" />

                        </div>

                        <div class="fed_m_hover" '.$this->get_style($template['header']['sub_title']).'>
                            <input
                                    class="form-control sub_title"
                                    value="'.$template['header']['sub_title']['title'].'"
                                    placeholder="Sub Title"
                                    name="Template1['.$random.'][header][sub_title][title]"
                            />
                        </div>
                        <div class=" fed_m_hover" '.$this->get_style($template['header']['price_value']).'>
                            <input
                                    class="form-control"
                                    value="'.$template['header']['price_value']['title'].'"
                                    placeholder="10"
                                    name="Template1['.$random.'][header][price_value][title]"
                            />

                            <div class="fed_m_hover" '.$this->get_style($template['header']['currency']).'>
                                '.fed_get_input_details(array(
                            'input_value' => fed_get_currency_type_key_value(),
                            'input_meta'  => 'Template1['.$random.'][header][currency][title]',
                            'input_type'  => 'select',
                            'user_value'  => $template['header']['currency']['title'],
                    )).'
                            </div>
                            <div class="fed_m_hover" '.$this->get_style($template['header']['month']).'>
                                <input
                                        placeholder="/mo"
                                        class="form-control"
                                        value="'.$template['header']['month']['title'].'"
                                        name="Template1['.$random.'][header][month][title]"
                                />
                            </div>
                            <div class="fed_m_membership_extra_container padd_top_20">
                                <div class="fed_m_hover fed_m_user_role">
                                <label>Select User Role</label>
                                    '.fed_get_input_details(array(
                            'input_value' => fed_get_user_roles(),
                            'input_meta'  => 'Template1['.$random.'][payments][roles]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['roles']) ? $template['payments']['roles'] : '',
                    )).'
                                </div>

                                <div class="checkbox fed_m_hover fed_one_time_payment">
                                    <label>
                                        <input type="radio" name="Template1['.$random.'][payments][type]"
                                               value="single" '.$payment_type_one.' />
                                        One Time
                                    </label>
                                    '.fed_get_input_details(array(
                            'input_value' => fed_m_get_one_time_payment(),
                            'input_meta'  => 'Template1['.$random.'][payments][type_value_one_time]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['type_value_one_time']) ? $template['payments']['type_value_one_time'] : '',
                    )).'
                                </div>
                                <div class="checkbox fed_m_hover fed_subscription_payment">
                                    <label>
                                        <input type="radio" name="Template1['.$random.'][payments][type]"
                                               value="subscription" '.$payment_type_sub.'>Subscription</label>
                                    '.fed_get_input_details(array(
                            'input_value' => $subscription_payment,
                            'input_meta'  => 'Template1['.$random.'][payments][type_value_subscription]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['type_value_subscription']) ? $template['payments']['type_value_subscription'] : ' ',
                    )).'
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pricing-content fed_m_hover fed_data_url_container" data-url="'.admin_url('admin-ajax.php?action=fed_m_add_template_content&random_key='.$random.'&fed_nonce='.wp_create_nonce("fed_nonce")).'"  '.$this->get_style($template['body']).'>';

            foreach ($template['body']['content'] as $index => $content) {
                $random_text = fed_get_random_string(4);
                $html        .= '<li class="fed_m_single_pricing_container fed_m_hover" '.$this->get_style($content).'>
                                <div class="fed_flex_start_center">
                                    <div><input type="text" class="form-control"
                                                name="Template1['.$random.'][body][content]['.$random_text.'][title]"
                                                value="'.$content['title'].'" placeholder="Text"/></div>
                                    <div class="fed_mouser_pointer">
                                        <i class="fa fa-plus fed_m_add_content" aria-hidden="true"></i>
                                        <i class="fa fa-trash fed_m_remove_content" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>';
            }
            $html .= '</ul>
                    <div class="fed_m_hover" '.$this->get_style($template['footer']).'>
                        <div
                                class="btn btn-primary fed_m_hover" '.$this->get_style($template['footer']['content']).'>
                            <input placeholder="Purchase" type="text" class="form-control"
                                   name="Template1['.$random.'][footer][content][title]"
                                   value="'.$template['footer']['content']['title'].'"/>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return $html;
    }

    /**
     * @param bool $single
     *
     * @return array
     */
    public function get_template_values($single = false)
    {
        if ($single) {
            return $this->template_values();
        }
        $template = isset($this->settings['selected_template']) ? $this->settings['selected_template'] : 'Template1';

        return isset($this->settings['template'][$template]['value']) ? $this->settings['template'][$template]['value'] : $this->template_values();

    }

    /**
     * @param $content
     *
     * @return array
     */
    public function template_values($content = array())
    {
        return array(
                array(
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
                                'content'    => array(
                                        array(
                                                'title'      => isset($content['body']['content']['title']) && null !== $content['body']['content']['title'] ? fed_sanitize_text_field($content['body']['content']['title']) : '50GB Disk Space',
                                                'bg_color'   => isset($content['body']['content']['bg_color']) && null !== $content['body']['content']['bg_color'] ? fed_sanitize_text_field($content['body']['content']['bg_color']) : null,
                                                'font_color' => isset($content['body']['content']['font_color']) && null !== $content['body']['content']['font_color'] ? fed_sanitize_text_field($content['body']['content']['font_color']) : null,
                                                'font_size'  => isset($content['body']['content']['font_size']) && null !== $content['body']['content']['font_size'] ? fed_sanitize_text_field($content['body']['content']['font_size']) : null,
                                        ),
                                        array(
                                                'title'      => isset($content['body']['content']['title']) && null !== $content['body']['content']['title'] ? fed_sanitize_text_field($content['body']['content']['title']) : '50 Email Accounts',
                                                'bg_color'   => isset($content['body']['content']['bg_color']) && null !== $content['body']['content']['bg_color'] ? fed_sanitize_text_field($content['body']['content']['bg_color']) : null,
                                                'font_color' => isset($content['body']['content']['font_color']) && null !== $content['body']['content']['font_color'] ? fed_sanitize_text_field($content['body']['content']['font_color']) : null,
                                                'font_size'  => isset($content['body']['content']['font_size']) && null !== $content['body']['content']['font_size'] ? fed_sanitize_text_field($content['body']['content']['font_size']) : null,
                                        ),
                                        array(
                                                'title'      => isset($content['body']['content']['title']) && null !== $content['body']['content']['title'] ? fed_sanitize_text_field($content['body']['content']['title']) : '50GB Monthly Bandwidth',
                                                'bg_color'   => isset($content['body']['content']['bg_color']) && null !== $content['body']['content']['bg_color'] ? fed_sanitize_text_field($content['body']['content']['bg_color']) : null,
                                                'font_color' => isset($content['body']['content']['font_color']) && null !== $content['body']['content']['font_color'] ? fed_sanitize_text_field($content['body']['content']['font_color']) : null,
                                                'font_size'  => isset($content['body']['content']['font_size']) && null !== $content['body']['content']['font_size'] ? fed_sanitize_text_field($content['body']['content']['font_size']) : null,
                                        ),
                                        array(
                                                'title'      => isset($content['body']['content']['title']) && null !== $content['body']['content']['title'] ? fed_sanitize_text_field($content['body']['content']['title']) : '10 Subdomains',
                                                'bg_color'   => isset($content['body']['content']['bg_color']) && null !== $content['body']['content']['bg_color'] ? fed_sanitize_text_field($content['body']['content']['bg_color']) : null,
                                                'font_color' => isset($content['body']['content']['font_color']) && null !== $content['body']['content']['font_color'] ? fed_sanitize_text_field($content['body']['content']['font_color']) : null,
                                                'font_size'  => isset($content['body']['content']['font_size']) && null !== $content['body']['content']['font_size'] ? fed_sanitize_text_field($content['body']['content']['font_size']) : null,
                                        ),
                                        array(
                                                'title'      => isset($content['body']['content']['title']) && null !== $content['body']['content']['title'] ? fed_sanitize_text_field($content['body']['content']['title']) : '15 Domains',
                                                'bg_color'   => isset($content['body']['content']['bg_color']) && null !== $content['body']['content']['bg_color'] ? fed_sanitize_text_field($content['body']['content']['bg_color']) : null,
                                                'font_color' => isset($content['body']['content']['font_color']) && null !== $content['body']['content']['font_color'] ? fed_sanitize_text_field($content['body']['content']['font_color']) : null,
                                                'font_size'  => isset($content['body']['content']['font_size']) && null !== $content['body']['content']['font_size'] ? fed_sanitize_text_field($content['body']['content']['font_size']) : null,
                                        ),
                                ),
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
                        ),
                        'payments'   => array(
                                'type'                    => isset($content['payments']['type']) && null !== $content['payments']['type'] ? fed_sanitize_text_field($content['payments']['type']) : null,
                                'roles'                   => isset($content['payments']['roles']) && null !== $content['payments']['roles'] ? fed_sanitize_text_field($content['payments']['roles']) : null,
                                'type_value_subscription' => isset($content['payments']['type_value_subscription']) && null !== $content['payments']['type_value_subscription'] ? fed_sanitize_text_field($content['payments']['type_value_subscription']) : null,
                                'type_value_one_time'     => isset($content['payments']['type_value_one_time']) && null !== $content['payments']['type_value_one_time'] ? fed_sanitize_text_field($content['payments']['type_value_one_time']) : null,
                        ),
                        'bg_color'   => isset($content['bg_color']) && null !== $content['bg_color'] ? fed_sanitize_text_field($content['bg_color']) : null,
                        'font_color' => isset($content['font_color']) && null !== $content['font_color'] ? fed_sanitize_text_field($content['font_color']) : null,
                        'font_size'  => isset($content['font_size']) && null !== $content['font_size'] ? fed_sanitize_text_field($content['font_size']) : null,
                ),
        );
    }

    /**
     * @param $type
     *
     * @return string
     */
    public function get_style($type)
    {
        $bg_color   = (isset($type['bg_color']) && ! empty($type['bg_color']) && null !== $type['bg_color']) ? 'background:'.$type['bg_color'].';' : '';
        $font_color = (isset($type['font_color']) && ! empty($type['font_color']) && null !== $type['font_color']) ? 'color:'.$type['font_color'].';' : '';
        $font_size  = (isset($type['font_size']) && ! empty($type['font_size']) && null !== $type['font_size']) ? 'font-size:'.$type['font_size'].'px;' : '';

        return 'style="'.$bg_color.' '.$font_color.' '.$font_size.'"';
    }

    /**
     * @param array $content
     */
    public function add_content()
    {
        $html                 = '';
        $templates            = $this->get_template_values();
        $subscription_payment = fed_m_get_subscription_payment();
//        var_dump($this->settings );
//        var_dump($templates);
        foreach ($templates as $template) {
            $random            = fed_get_random_string(5);
            $color             = '#0AAAAA';
            $border_color      = '#0AAAAA';
            $border_left_color = '#0AAAAA';
            if (isset($template['extra']['border']['color']) && null !== $template['extra']['border']['color']) {
                $color             = $template['extra']['border']['color'];
                $border_color      = 'border-color:'.$template['extra']['border']['color'].';';
                $border_left_color = 'border-color:'.$template['extra']['border']['color'].';';
            }

            $border_size = isset($template['extra']['border']['size']) && null !== $template['extra']['border']['size'] ? $template['extra']['border']['size'].'px' : '2px';

            $border_type = isset($template['extra']['border']['type']) && null !== $template['extra']['border']['type'] ? $template['extra']['border']['type'] : 'solid';
            $border      = $border_size.' '.$border_type.' '.$color;

            $payment_type_one = isset($template['payments']['type']) && $template['payments']['type'] === 'single' ? 'checked' : '';
            $payment_type_sub = isset($template['payments']['type']) && $template['payments']['type'] === 'subscription' ? 'checked' : '';


            $html .= '<div class="fed_m_template">';
            $html .= '
            <style>
                .bc_fed .template1 .pricingTable .read:hover {
                '.$border_color.';color:'.$color.';
                }

                .bc_fed .template1 .pricingTable .read:hover:before,
                .bc_fed .template1 .pricingTable .read:hover:after {
                '.$border_left_color.';
                }

                .bc_fed .template1 .pricingTable:after {
                    border-bottom: '.$border.';
                    border-top: '.$border.';
                    transform: scaleX(0);
                    transform-origin: 0 100% 0;
                }

                .bc_fed .template1 .pricingTable:before {
                    border-right: '.$border.';
                    border-left: '.$border.';
                    transform: scaleY(0);
                    transform-origin: 100% 0 0;
                }
            </style>';

            $html .= '<div class="pull-right padd_top_20">
                    <button class="btn btn-primary fed_m_add_membership" data-url="'.admin_url('admin-ajax.php?action=fed_m_add_template&fed_nonce='.wp_create_nonce("fed_nonce")).'">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>

                    <button class="btn btn-danger fed_m_remove_membership">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="clearfix"></div>';

            $html .= '<div class="pricingTable initial fed_m_hover" '.$this->get_style($template).'>
                    <div class="pricingTable-header fed_m_hover" '.$this->get_style($template['header']['heading']).'>
                        <div class="heading fed_m_hover" '.$this->get_style($template['header']['heading']).'>
                            <input '.$this->get_style($template['header']['heading']).' class="form-control fed_m_hover" placeholder="Title" value=" '.$template['header']['heading']['title'].' " name="Template1['.$random.'][header][heading][title]" />

                        </div>

                        <div class="fed_m_hover" '.$this->get_style($template['header']['sub_title']).'>
                            <input
                                    class="form-control sub_title"
                                    value="'.$template['header']['sub_title']['title'].'"
                                    placeholder="Sub Title"
                                    name="Template1['.$random.'][header][sub_title][title]"
                            />
                        </div>
                        <div class=" fed_m_hover" '.$this->get_style($template['header']['price_value']).'>
                            <input
                                    class="form-control"
                                    value="'.$template['header']['price_value']['title'].'"
                                    placeholder="10"
                                    name="Template1['.$random.'][header][price_value][title]"
                            />

                            <div class="fed_m_hover" '.$this->get_style($template['header']['currency']).'>
                                '.fed_get_input_details(array(
                            'input_value' => fed_get_currency_type_key_value(),
                            'input_meta'  => 'Template1['.$random.'][header][currency][title]',
                            'input_type'  => 'select',
                            'user_value'  => $template['header']['currency']['title'],
                    )).'
                            </div>
                            <div class="fed_m_hover" '.$this->get_style($template['header']['month']).'>
                                <input
                                        placeholder="/mo"
                                        class="form-control"
                                        value="'.$template['header']['month']['title'].'"
                                        name="Template1['.$random.'][header][month][title]"
                                />
                            </div>
                            <div class="fed_m_membership_extra_container padd_top_20">
                                <div class="fed_m_hover fed_m_user_role">
                                <label>Select User Role</label>
                                    '.fed_get_input_details(array(
                            'input_value' => fed_get_user_roles(),
                            'input_meta'  => 'Template1['.$random.'][payments][roles]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['roles']) ? $template['payments']['roles'] : '',
                    )).'
                                </div>

                                <div class="checkbox fed_m_hover fed_one_time_payment">
                                    <label>
                                        <input type="radio" name="Template1['.$random.'][payments][type]"
                                               value="single" '.$payment_type_one.' />
                                        One Time
                                    </label>
                                    '.fed_get_input_details(array(
                            'input_value' => fed_m_get_one_time_payment(),
                            'input_meta'  => 'Template1['.$random.'][payments][type_value_one_time]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['type_value_one_time']) ? $template['payments']['type_value_one_time'] : '',
                    )).'
                                </div>
                                <div class="checkbox fed_m_hover fed_subscription_payment">
                                    <label>
                                        <input type="radio" name="Template1['.$random.'][payments][type]"
                                               value="subscription" '.$payment_type_sub.'>Subscription</label>
                                    '.fed_get_input_details(array(
                            'input_value' => $subscription_payment,
                            'input_meta'  => 'Template1['.$random.'][payments][type_value_subscription]',
                            'input_type'  => 'select',
                            'user_value'  => isset($template['payments']['type_value_subscription']) ? $template['payments']['type_value_subscription'] : ' ',
                    )).'
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="pricing-content fed_m_hover fed_data_url_container" data-url="'.admin_url('admin-ajax.php?action=fed_m_add_template_content&random_key='.$random.'&fed_nonce='.wp_create_nonce("fed_nonce")).'"  '.$this->get_style($template['body']).'>';

            foreach ($template['body']['content'] as $index => $content) {
                $random_text = fed_get_random_string(4);
                $html        .= '<li class="fed_m_single_pricing_container fed_m_hover" '.$this->get_style($content).'>
                                <div class="fed_flex_start_center">
                                    <div><input type="text" class="form-control"
                                                name="Template1['.$random.'][body][content]['.$random_text.'][title]"
                                                value="'.$content['title'].'" placeholder="Text"/></div>
                                    <div class="fed_mouser_pointer">
                                        <i class="fa fa-plus fed_m_add_content" aria-hidden="true"></i>
                                        <i class="fa fa-trash fed_m_remove_content" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </li>';
            }
            $html .= '</ul>
                    <div class="fed_m_hover" '.$this->get_style($template['footer']).'>
                        <div
                                class="btn btn-primary fed_m_hover" '.$this->get_style($template['footer']['content']).'>
                            <input placeholder="Purchase" type="text" class="form-control"
                                   name="Template1['.$random.'][footer][content][title]"
                                   value="'.$template['footer']['content']['title'].'"/>
                        </div>
                    </div>
                </div>
            </div>';
        }

        return $html;
    }

    /**
     * @param $content
     */
    public function template($content = array())
    {
        $color             = '#0AAAAA';
        $border_color      = '#0AAAAA';
        $border_left_color = '#0AAAAA';
        if (isset($this->template['extra']['border']['color']) && null !== $this->template['extra']['border']['color']) {
            $color             = $this->template['extra']['border']['color'];
            $border_color      = 'border-color:'.$this->template['extra']['border']['color'].';';
            $border_left_color = 'border-color:'.$this->template['extra']['border']['color'].';';
        }

        $border_size = isset($this->template['extra']['border']['size']) && null !== $this->template['extra']['border']['size'] ? $this->template['extra']['border']['size'].'px' : '2px';

        $border_type = isset($this->template['extra']['border']['type']) && null !== $this->template['extra']['border']['type'] ? $this->template['extra']['border']['type'] : 'solid';
        $border      = $border_size.' '.$border_type.' '.$color;
        ?>
        <style>
            .bc_fed .template1 .pricingTable .read:hover {
            <?php echo $border_color;?> <?php echo 'color:'. $color .';'?>
            }

            .bc_fed .template1 .pricingTable .read:hover:before,
            .bc_fed .template1 .pricingTable .read:hover:after {
            <?php echo $border_left_color; ?>
            }

            .bc_fed .template1 .pricingTable:after {
                border-bottom: <?php echo $border ?>;
                border-top: <?php echo $border ?>;
                transform: scaleX(0);
                transform-origin: 0 100% 0;
            }

            .bc_fed .template1 .pricingTable:before {
                border-right: <?php echo $border ?>;
                border-left: <?php echo $border ?>;
                transform: scaleY(0);
                transform-origin: 100% 0 0;
            }


        </style>
        <div class="text-center padd_bot_10">
            <button class="btn btn-primary">
                <i class="fa fa-check"></i>
                Selected
            </button>
        </div>
        <div class="pricingTable" <?php echo $this->get_style($this->template); ?>>
            <div class="pricingTable-header" <?php echo $this->get_style($this->template['header']['heading']); ?>>
                <h3 class="heading" <?php echo $this->get_style($this->template['header']['heading']); ?>>
                    <?php echo $this->template['header']['heading']['title'] ?>
                </h3>
                <span class="sub_title" <?php echo $this->get_style($this->template['header']['sub_title']); ?>>
                    <?php echo $this->template['header']['sub_title']['title'] ?>
                </span>
                <div class="price_value" <?php echo $this->get_style($this->template['header']['price_value']); ?>>
                    <?php echo $this->template['header']['price_value']['title'] ?>
                    <span class="currency" <?php echo $this->get_style($this->template['header']['currency']); ?>>
                        <?php echo $this->template['header']['currency']['title'] ?>
                    </span>
                    <span class="month" <?php echo $this->get_style($this->template['header']['month']); ?>>
                        <?php echo $this->template['header']['month']['title'] ?>
                    </span>
                </div>
            </div>
            <ul class="pricing-content" <?php echo $this->get_style($this->template['body']); ?>>
                <?php
                foreach ($this->template['body']['content'] as $content) {
                    ?>
                    <li <?php echo $this->get_style($content); ?>><?php echo $content['title'] ?></li>
                <?php } ?>
            </ul>
            <div <?php echo $this->get_style($this->template['footer']); ?>>
                <a href="<?php echo isset($content['url']) && ! empty($content['url']) ? $content['url'] : '' ?>"
                   class="read" <?php echo $this->get_style($this->template['footer']['content']); ?>>
                    <?php echo $this->template['footer']['content']['title'] ?>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
        <?php
    }

    public function show_template()
    {
        $template_value    = $this->get_template_values();
        $color             = '#0AAAAA';
        $border_color      = '#0AAAAA';
        $border_left_color = '#0AAAAA';
        $current_user_role = fed_get_current_user_role_key();
        if (isset($this->template['extra']['border']['color']) && null !== $this->template['extra']['border']['color']) {
            $color             = $this->template['extra']['border']['color'];
            $border_color      = 'border-color:'.$this->template['extra']['border']['color'].';';
            $border_left_color = 'border-color:'.$this->template['extra']['border']['color'].';';
        }

        $border_size = isset($this->template['extra']['border']['size']) && null !== $this->template['extra']['border']['size'] ? $this->template['extra']['border']['size'].'px' : '2px';

        $border_type = isset($this->template['extra']['border']['type']) && null !== $this->template['extra']['border']['type'] ? $this->template['extra']['border']['type'] : 'solid';
        $border      = $border_size.' '.$border_type.' '.$color;
        ?>
        <style>
            .bc_fed .template1 .pricingTable .read:hover {
            <?php echo $border_color;?> <?php echo 'color:'. $color .';'?>
            }

            .bc_fed .template1 .pricingTable .read:hover:before,
            .bc_fed .template1 .pricingTable .read:hover:after {
            <?php echo $border_left_color; ?>
            }

            .bc_fed .template1 .pricingTable:after {
                border-bottom: <?php echo $border ?>;
                border-top: <?php echo $border ?>;
                transform: scaleX(0);
                transform-origin: 0 100% 0;
            }

            .bc_fed .template1 .pricingTable:before {
                border-right: <?php echo $border ?>;
                border-left: <?php echo $border ?>;
                transform: scaleY(0);
                transform-origin: 100% 0 0;
            }


        </style>
        <div class="bc_fed">
            <div class="template1">
                <?php
                foreach ($template_value as $template) {
                    ?>
                    <div class="fed_m_template1_pricing_container">
                        <div class="pricingTable" <?php echo $this->get_style($template); ?>>
                            <div class="pricingTable-header" <?php echo $this->get_style($template['header']['heading']); ?>>
                                <h3 class="heading" <?php echo $this->get_style($template['header']['heading']); ?>>
                                    <?php echo $template['header']['heading']['title'] ?>
                                </h3>
                                <span class="sub_title" <?php echo $this->get_style($template['header']['sub_title']); ?>>
                    <?php echo $template['header']['sub_title']['title'] ?>
                </span>
                                <div class="price_value" <?php echo $this->get_style($template['header']['price_value']); ?>>
                                    <?php echo $template['header']['price_value']['title'] ?>
                                    <span class="currency" <?php echo $this->get_style($template['header']['currency']); ?>>
                        <?php echo fed_get_currency_symbol($template['header']['currency']['title']) ?>
                    </span>
                                    <span class="month" <?php echo $this->get_style($template['header']['month']); ?>>
                        <?php echo $template['header']['month']['title'] ?>
                    </span>
                                </div>
                            </div>
                            <ul class="pricing-content" <?php echo $this->get_style($template['body']); ?>>
                                <?php
                                foreach ($template['body']['content'] as $content) {
                                    ?>
                                    <li <?php echo $this->get_style($content); ?>><?php echo $content['title'] ?></li>
                                <?php } ?>
                            </ul>

                            <div <?php echo $this->get_style($template['footer']); ?>>
                                <div data-url="<?php echo isset($template['extra']['url']) && ! empty($template['extra']['url']) ? $template['extra']['url'] : '' ?>"
                                     class="fed_m_membership_button read" <?php echo $this->get_style($template['footer']['content']); ?>>

                                    <?php if ($template['payments']['roles'] === $current_user_role) { ?>
                                        <div class="fed_red_color">
                                            <?php
                                            echo __('Purchased', 'frontend-dashboard-membership');
                                            //                                        echo __('You have <br>'). __('already <br>'). $template['footer']['content']['title'] . __('<br>this plan');
                                            ?>
                                        </div>
                                    <?php } else {
                                        echo $template['footer']['content']['title'];
                                    } ?>
                                    <span>
                                    <i class="fa fa-angle-right"></i>
                                        </span>
                                </div>
                            </div>


                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }


}

new FED_M_Template1();