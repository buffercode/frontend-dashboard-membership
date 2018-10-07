<?php

namespace FED_Membership;


use FED_PayPal\FED_PayPal;

/**
 * Class FED_M_Menu
 *
 * @package FED_Membership
 */
class FED_M_Menu
{
    /**
     * FEDE_Menu constructor.
     */
    public function __construct()
    {
        add_filter('fed_add_main_sub_menu', array(
                $this,
                'fed_m_add_main_sub_menu',
        ));

        add_filter('fed_admin_dashboard_settings_menu_header', array(
                $this,
                'fed_m_admin_dashboard_settings_menu_header',
        ));

        add_filter('fed_admin_script_loading_pages', array(
                $this,
                'fed_m_admin_script_loading_pages',
        ));

        add_action('fed_enqueue_script_style_admin', array($this, 'script_style_admin'));
        add_action('fed_enqueue_script_style_frontend', array($this, 'script_style_admin'));

//        add_action('wp_ajax_fed_admin_paypal_setting_form', array($this, 'fed_pay_admin_paypal_api_save'));
    }

//    public function fed_pay_admin_paypal_api_save()
//    {
//        $request                                 = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
//        $fed_admin_settings_paypal_api           = get_option('fed_admin_settings_payments');
//        $fed_admin_settings_paypal_api['paypal'] = array(
//                'api' => array(
//                        'type'               => fed_isset_sanitize($request['paypal']['api']['type']),
//                        'success_url'        => fed_isset_sanitize($request['paypal']['api']['success_url']),
//                        'cancel_url'         => fed_isset_sanitize($request['paypal']['api']['cancel_url']),
//                        'sandbox_client_id'  => fed_isset_sanitize($request['paypal']['api']['sandbox_client_id']),
//                        'sandbox_secrete_id' => fed_isset_sanitize($request['paypal']['api']['sandbox_secrete_id']),
//                        'live_client_id'     => fed_isset_sanitize($request['paypal']['api']['live_client_id']),
//                        'live_secrete_id'    => fed_isset_sanitize($request['paypal']['api']['live_secrete_id']),
//                ),
//        );
//
//        $new_settings = apply_filters('fed_admin_settings_payments_paypal_api_save', $fed_admin_settings_paypal_api,
//                $request);
//
//        update_option('fed_admin_settings_payments', $new_settings);
//
//        wp_send_json_success(array(
//                'message' => __('PayPal API Updated Successfully '),
//        ));
//    }


    /**
     * @param $menu
     *
     * @return mixed
     */
    public function fed_m_add_main_sub_menu($menu)
    {
        $menu['fed_membership_menu'] = array(
                'page_title' => __('Membership', 'frontend-dashboard-membership'),
                'menu_title' => __('Membership', 'frontend-dashboard-membership'),
                'capability' => 'manage_options',
                'callback'   => array($this, 'fed_membership_menu'),
                'position'   => 30,
        );

        return $menu;
    }

    public function fed_membership_menu()
    {
//        $paypal = new FED_PayPal();
//        $i = $paypal->get_payment_by_id('PAY-5MS68422P2668760PLN7FTGY');
//        bcdump($i);
        $request = fed_sanitize_text_field($_REQUEST);
        ?>
        <div class="bc_fed container">
            <div class="bc_fed_wrapper padd_top_20">
                <?php
                if (isset($request['mpage'])) {
                    $template = new FED_M_Templates();
                    if ($request['mpage'] === 'select_layouts') {
                        $template->create_layouts();
                    } elseif ($request['mpage'] === 'layout_content') {
                        $template->layout_content();
                    }
                } else {
                    $this->membership_dropdown_menu();
                }
                ?>
            </div>
        </div>

        <?php
    }

    public function membership_dropdown_menu()
    {
        ?>
        <div class="row padd_top_20">
            <div class="col-md-12">
                <div class="dropdown fed_payment_dropdown open">
                    <button class="btn btn-primary dropdown-toggle" type="button" id="fed_membership"
                            data-toggle="dropdown">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        Membership
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="membership">
                        <li role="separator" class="divider"></li>
                        <li role="presentation">
                            <a role="menuitem" class=""
                               href="<?php echo menu_page_url('fed_membership_menu',
                                               false).'&mpage=select_layouts'; ?>">
                                <i class="fa fa-dice-one" aria-hidden="true"></i>
                                Select Membership Layout
                            </a>
                        </li>
                        <li role="presentation">
                            <a role="menuitem" class=""
                               href="<?php echo menu_page_url('fed_membership_menu',
                                               false).'&mpage=layout_content'; ?>">
                                <i class="fa fa-dice-two" aria-hidden="true"></i>
                                Membership Layout Content
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?

    }


    /**
     * Top Menu Header
     *
     * @param array $menu
     *
     * @return array
     */
    public function fed_m_admin_dashboard_settings_menu_header($menu)
    {
        $settings           = get_option('fed_admin_settings_payments');
        $menu['membership'] = array(
                'icon_class' => 'fa fa-users',
                'name'       => __('Membership', 'frontend-dashboard-membership'),
                'callable'   => array(
                        'object'     => $this,
                        'method'     => 'admin_membership_menu',
                        'parameters' => array('settings' => $settings),
                ),
        );

        return $menu;
    }

    /**
     * @param $fed_admin_options
     */
    public function admin_membership_menu($fed_admin_options)
    {

        $tabs = $this->fed_m_admin_menu_options($fed_admin_options);
        fed_common_layouts_admin_settings($fed_admin_options, $tabs);
    }

    /**
     * @param $fed_admin_options
     *
     * @return array
     */
    public function fed_m_admin_menu_options($fed_admin_options)
    {
        $options = array(
                'fed_pay_admin_paypal_settings' => array(
                        'icon'      => 'fas fa-bars',
                        'name'      => __('Menu Settings', 'frontend-dashboard-membership'),
                        'callable'  => array('object' => $this, 'method' => 'fed_m_admin_membership_menu'),
                        'arguments' => $fed_admin_options,
                ),
        );

        return apply_filters('fed_m_customize_admin_menu', $options, $fed_admin_options);
    }

    /**
     * Scripts
     *
     * @param $pages
     *
     * @return array
     */
    public function fed_m_admin_script_loading_pages($pages)
    {

        return array_merge($pages, array('fed_membership_menu'));
    }

    /**
     * Style
     */
    public function script_style_admin()
    {
        wp_enqueue_style('fed_membership_admin_style',
                plugins_url('assets/fed_membership.css', BC_FED_M_PLUGIN),
                array(), BC_FED_M_PLUGIN_VERSION, 'all');

        wp_enqueue_script('fed_membership_admin_script', plugins_url('assets/fed_membership.js', BC_FED_M_PLUGIN),
                array());
    }

    /**
     * @param $fed_admin_options
     */
    public function fed_m_admin_membership_menu($fed_admin_options)
    {
        echo 'Under Construction';
//        $options = $this->get_admin_membership_menu($fed_admin_options);
//        fed_common_simple_layout($options);
    }

    /**
     * @param $fed_admin_options
     *
     * @return array
     */
    private function get_admin_membership_menu($fed_admin_options)
    {
        return array(
                'form'  => array(
                        'method' => '',
                        'class'  => 'fed_admin_menu fed_ajax',
                        'attr'   => '',
                        'action' => array('url' => '', 'action' => 'fed_m_save_admin_menu_setting'),
                        'nonce'  => array('action' => '', 'name' => ''),
                        'loader' => '',
                ),
                'input' => array(
                        'Menu Name'                 => array(
                                'col'          => 'col-md-6',
                                'name'         => __('Menu Name', 'frontend-dashboard-membership'),
                                'input'        => fed_get_input_details(array(
                                        'placeholder' => __('Please enter Membership Menu Name',
                                                'frontend-dashboard-membership'),
                                        'input_meta'  => 'paypal[api][sandbox_client_id]',
                                        'user_value'  => isset($fed_admin_options['settings']['paypal']['api']['sandbox_client_id']) ? $fed_admin_options['settings']['paypal']['api']['sandbox_client_id'] : '',
                                        'input_type'  => 'single_line',
                                )),
                                'help_message' => fed_show_help_message(array('content' => "Please check What is <a href='https://developer.paypal.com/docs/classic/lifecycle/ug_sandbox/'>Sandbox</a> | <a href='https://developer.paypal.com/docs/classic/lifecycle/goingLive/'>Live</a>")),
                        ),
                        'PayPal Success URL'        => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Success URL', 'frontend-dashboard-membership'),
                                'input'        =>
                                        wp_dropdown_pages(array(
                                                'name'  => 'paypal[api][success_url]',
                                                'class' => 'form-control',
                                                'echo'  => false,
                                        )),
                                'help_message' => fed_show_help_message(array('content' => "After Payment Success it will be redirect to this page")),
                        ),
                        'PayPal Cancel URL'         => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Cancel URL', 'frontend-dashboard-membership'),
                                'input'        => wp_dropdown_pages(array(
                                        'name'  => 'paypal[api][cancel_url]',
                                        'class' => 'form-control',
                                        'echo'  => false,
                                )),
                                'help_message' => fed_show_help_message(array('content' => "After Payment Cancelled it will be redirect to this page")),
                        ),
                        'PayPal Sandbox Client ID'  => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Sandbox Client ID', 'frontend-dashboard-membership'),
                                'input'        => fed_get_input_details(array(
                                        'placeholder' => __('Please enter PayPal Sandbox Client ID',
                                                'frontend-dashboard-membership'),
                                        'input_meta'  => 'paypal[api][sandbox_client_id]',
                                        'user_value'  => isset($fed_admin_options['settings']['paypal']['api']['sandbox_client_id']) ? $fed_admin_options['settings']['paypal']['api']['sandbox_client_id'] : '',
                                        'input_type'  => 'single_line',
                                )),
                                'help_message' => fed_show_help_message(array('content' => "Please login in to PayPal and use this <a href='https://developer.paypal.com/developer/applications/'>PayPal API</a> to create the REST API apps")),
                        ),
                        'PayPal Sandbox Secrete ID' => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Sandbox Secrete ID', 'frontend-dashboard-membership'),
                                'input'        => fed_get_input_details(array(
                                        'placeholder' => __('Please enter PayPal Sandbox Secrete ID',
                                                'frontend-dashboard-membership'),
                                        'input_meta'  => 'paypal[api][sandbox_secrete_id]',
                                        'user_value'  => isset($fed_admin_options['settings']['paypal']['api']['sandbox_secrete_id']) ? $fed_admin_options['settings']['paypal']['api']['sandbox_secrete_id'] : '',
                                        'input_type'  => 'single_line',
                                )),
                                'help_message' => fed_show_help_message(array('content' => "Please login in to PayPal and use this <a href='https://developer.paypal.com/developer/applications/'>PayPal API</a> to create the REST API apps")),
                        ),
                        'PayPal Live Client ID'     => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Live Client ID', 'frontend-dashboard-membership'),
                                'input'        => fed_get_input_details(array(
                                        'placeholder' => __('Please enter PayPal Live Client ID',
                                                'frontend-dashboard-membership'),
                                        'input_meta'  => 'paypal[api][live_client_id]',
                                        'user_value'  => isset($fed_admin_options['settings']['paypal']['api']['live_client_id']) ? $fed_admin_options['settings']['paypal']['api']['live_client_id'] : '',
                                        'input_type'  => 'single_line',
                                )),
                                'help_message' => fed_show_help_message(array('content' => "Please login in to PayPal and use this <a href='https://developer.paypal.com/developer/applications/'>PayPal API</a> to create the REST API apps")),
                        ),
                        'PayPal Live Secrete ID'    => array(
                                'col'          => 'col-md-6',
                                'name'         => __('PayPal Live Secrete ID', 'frontend-dashboard-membership'),
                                'input'        => fed_get_input_details(array(
                                        'placeholder' => __('Please enter PayPal Live Secrete ID',
                                                'frontend-dashboard-membership'),
                                        'input_meta'  => 'paypal[api][live_secrete_id]',
                                        'user_value'  => isset($fed_admin_options['settings']['paypal']['api']['live_secrete_id']) ? $fed_admin_options['settings']['paypal']['api']['live_secrete_id'] : '',
                                        'input_type'  => 'single_line',
                                )),
                                'help_message' => fed_show_help_message(array('content' => "Please login in to PayPal and use this <a href='https://developer.paypal.com/developer/applications/'>PayPal API</a> to create the REST API apps")),
                        ),
                ),
        );
    }
}

new FED_M_Menu();