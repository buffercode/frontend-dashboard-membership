<?php

namespace FED_Membership;

use Exception;
use FED_Log;
use FED_PayPal;
use WP_User;

/**
 * Class FED_M_Membership
 *
 * @package FED_Membership
 */
class FED_M_Membership
{
    public function __construct()
    {
        add_action('wp_ajax_fed_m_payment_redirect', array(
                $this,
                'start_payment',
        ));
        add_action('template_redirect', array($this, 'paypal_success'));
        add_action('fed_paypal_single_after_save_action', array($this, 'update_user_role'), 10, 2);
        add_action('fed_paypal_subscription_after_save_action', array($this, 'update_user_role'), 10, 2);

    }

    /**
     * @param $data
     * @param $status
     */
    public function update_user_role($data, $status)
    {
        $options  = get_option('fed_m_templates');
        $template = 'Template1';
//        FED_Log::writeLog($options);
//        FED_Log::writeLog(gettype($options));
//        FED_Log::writeLog((isset($options['template'][$template]['value'])));
//        FED_Log::writeLog($options['template'][$template]['value']);
        if ($options && is_array($options) && isset($options['template'][$template]['value'])) {
//            FED_Log::writeLog('in before foreach');
            foreach ($options['template'][$template]['value'] as $option) {
                $plan_id = $option['payments']['type'] === 'single' ? $option['payments']['type_value_one_time'] : $option['payments']['type_value_subscription'];
//                FED_Log::writeLog('Loop');
//                FED_Log::writeLog($option['payments']['type_value_one_time']);
//                FED_Log::writeLog($option['payments']['type_value_subscription']);
//                FED_Log::writeLog($data['plan_id']);
//                FED_Log::writeLog($option['payments']['roles']);
                if ($plan_id === $data['plan_id']) {
                    //FED_Log::writeLog(array('role' => $option['payments']['roles'], 'id' => $data['user_id']));
                    $user = new WP_User($data['user_id']); //123 is the user ID
                    $user->set_role($option['payments']['roles']);
                    break;
                    //$user->roles; // ["subscriber"]
//                    FED_Log::writeLog('New Role');
//                    FED_Log::writeLog(array('role' => $user->roles, 'id' => $data['user_id']));
                }
            }
            fed_set_alert('fed_dashboard_top_message', apply_filters('fed_single_payment_success_message',
                    __('Payment Received Successfully', 'frontend-dashboard-membership')));
        }
        FED_Log::writeLog('out');
    }

    /**
     * PayPal Request Process.
     * TODO: PayPal
     */
    public function paypal_success()
    {
        $request = fed_sanitize_text_field($_REQUEST);
        if (isset($request['paymentId'], $request['PayerID'])) {
            $paypal = new FED_PayPal\FED_PayPal();
            $paypal->payment_success($request);
        }

        if (isset($request['token'], $request['fed_m_subscription'], $request['plan_id']) && ! empty($request['token'])) {
            $paypal = new FED_PayPal\FED_PayPal();
            $paypal->billing_agreement_success();
        }

        if (isset($request) && isset($request['fed_m_payment_status']) && $request['fed_m_payment_status'] === 'success') {
//            fed_set_alert('fed_dashboard_top_message', apply_filters('fed_single_payment_success_message',
//                    __('Payment Received Successfully', 'frontend-dashboard-membership')));
        }
    }

    /**
     * @return bool
     */
    public function start_payment()
    {
        $request = fed_sanitize_text_field($_REQUEST);

        if ( ! isset($request['type'], $request['id'])) {
            wp_die('Something went wrong, please reload the page and try');
        }

        $payment_type = fed_sanitize_text_field($request['type']);
        $plan_id      = fed_sanitize_text_field($request['id']);


        //Check the payment type
        if ($payment_type === 'single') {
            $paypal  = new FED_PayPal\FED_PayPal();
            $details = fed_fetch_table_rows_by_key_value(BC_FED_PAY_PAYMENT_PLAN_TABLE, 'plan_id', $plan_id);

            if ( ! $details || count($details) < 1) {
                FED_Log::writeLog($details, 'ERROR');
                wp_die('You are trying with Plan ID, which does not exist');
            }
            $details         = $details[0];
            $payment_details = $this->format_payment(
                    $details,
                    array(
                            'plan_type' => $details['plan_type'],
                            'plan_id'   => $details['plan_id'],
                            'plan_name' => $details['plan_name'],
                    )
            );

            return $paypal->payment_start($payment_details);
        }

        if ($payment_type === 'subscription') {
            $plan         = new FED_PayPal\FED_PayPal();
            $plan_details = $plan->get_plan_by_id($plan_id);
            try {
                $plan->create_active_billing_agreement($plan_details);
            } catch (Exception $e) {
                FED_Log::writeLog($e);
            }


        }


    }

    /**
     * @param       $details
     *
     * @param array $extra
     *
     * @return array
     */
    private function format_payment($details, $extra = null)
    {
        $paypal          = $item_list = array();
        $sub_total_array = array();
        $sub_total       = 0;
        $item_lists      = unserialize($details['item_lists']);
        $amount          = unserialize($details['amount']);

        foreach ($item_lists as $index => $lists) {

            $random         = fed_get_random_string(5);
            $quantity       = isset($lists['quantity']) ? (float)fed_sanitize_text_field($lists['quantity']) : 0;
            $price          = isset($lists['price']) ? (float)fed_sanitize_text_field($lists['price']) : 0;
            $item_tax       = isset($lists['tax']) ? (float)fed_sanitize_text_field($lists['tax']) : 0;
            $item_tax_type  = isset($lists['tax_type']) ? fed_sanitize_text_field($lists['tax_type']) : 'fixed';
            $price_quantity = (float)$quantity * $price;
            $item_tax_value = $this->get_tax($item_tax_type, $price_quantity, $item_tax);
            $price_value    = $price_quantity + $item_tax_value;
            $sub_total      = $sub_total + $price_value;

            $item_list[$index] = array(
                    'name'        => isset($lists['name']) ? fed_sanitize_text_field($lists['name']) : 'NO_NAME_GIVEN',
                    'currency'    => isset($amount['currency']) ? fed_sanitize_text_field($amount['currency']) : 'USD',
                    'description' => isset($lists['description']) ? fed_sanitize_text_field($lists['description']) : '',
                    'quantity'    => $quantity,
                    'url'         => isset($lists['url']) ? fed_sanitize_text_field($lists['url']) : null,
                    'sku'         => isset($lists['sku']) ? fed_sanitize_text_field($lists['sku']) : null,
                    'price'       => $price_value,
                    'tax'         => $item_tax_value,
            );

        }

        $shipping_discount       = isset($amount['details']['shipping_discount']) ? (float)fed_sanitize_text_field($amount['details']['shipping_discount']) : 0;
        $shipping_discount_type  = isset($amount['details']['shipping_discount_type']) ? fed_sanitize_text_field($amount['details']['shipping_discount_type']) : 'fixed';
        $shipping_discount_value = $this->get_tax($shipping_discount_type, $sub_total, $shipping_discount);

        $insurance       = isset($amount['details']['insurance']) ? (float)fed_sanitize_text_field($amount['details']['insurance']) : 0;
        $insurance_type  = isset($amount['details']['insurance_type']) ? fed_sanitize_text_field($amount['details']['insurance_type']) : 'fixed';
        $insurance_value = $this->get_tax($insurance_type, $sub_total, $insurance);

        $tax       = isset($amount['details']['tax']) ? (float)($amount['details']['tax']) : 0;
        $tax_type  = isset($amount['details']['tax_type']) ? fed_sanitize_text_field($amount['details']['tax_type']) : 'fixed';
        $tax_value = (float)$this->get_tax($tax_type, $sub_total, $tax);


//        $gift_wrap    = isset($amount['details']['gift_wrap']) ? (float)($amount['details']['gift_wrap']) : 0;
        $shipping     = isset($amount['details']['shipping']) ? (float)($amount['details']['shipping']) : 0;
        $handling_fee = isset($amount['details']['handling_fee']) ? (float)($amount['details']['handling_fee']) : 0;

        $total = $sub_total + $shipping + $tax_value + $handling_fee - $shipping_discount_value + $insurance_value;// + $gift_wrap;

        $paypal = array(
                'payments' => array(
                        'status'       => isset($details['status']) ? fed_sanitize_text_field($details['status']) : 'ACTIVE',
                        'transactions' => array(
                                'transaction1' => array(
                                        'item_list'      => $item_list,
                                        'amount'         => array(
                                                'currency' => isset($amount['currency']) ? fed_sanitize_text_field($amount['currency']) : 'USD',
                                                'total'    => $total,
                                                'details'  => array(
                                                        'sub_total'         => $sub_total,
                                                        'shipping'          => $shipping,
                                                        'tax'               => $tax_value,
                                                        'handling_fee'      => $handling_fee,
                                                        'shipping_discount' => $shipping_discount_value,
                                                        'insurance'         => $insurance_value,
//                                                        'gift_wrap'         => $gift_wrap,
                                                ),
                                        ),
                                        'description'    => isset($details['description']) ? fed_sanitize_text_field($details['description']) : '',
                                        'invoice_number' => current_time('YmdHis').'_'.fed_get_random_string(10),
                                        'reference_id'   => isset($details['reference_id']) ? fed_sanitize_text_field($details['reference_id']) : '',
                                        'note_to_payee'  => isset($details['note_to_payee']) ? fed_sanitize_text_field($details['note_to_payee']) : '',
                                        'purchase_order' => isset($details['purchase_order']) ? fed_sanitize_text_field($details['purchase_order']) : '',
                                        'custom'         => $extra !== null ? $extra : null,
                                ),
                        ),
                ),
        );

        return $paypal;

    }

    /**
     * @param $type
     * @param $total
     * @param $tax
     *
     * @return float|int
     */
    private function get_tax($type, $total, $tax)
    {
        $tax_value = 0;
        if ($type === 'percentage') {
            return (float)($total * $tax) / 100;
        }

        if ($type === 'fixed') {
            return $tax;
        }

        return (int)$tax_value;
    }
}

new FED_M_Membership();