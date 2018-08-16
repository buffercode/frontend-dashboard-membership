<?php

namespace FED_Membership;

use FED_PayPal;

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

    }

    public function start_payment()
    {
        $request = $_REQUEST;

        if ( ! isset($request['type'], $request['id'])) {
            wp_die('Something went wrong, please reload the page and try');
        }

        $payment_type = fed_sanitize_text_field($request['type']);
        $plan_id      = fed_sanitize_text_field($request['id']);


        //Check the payment type
        if ($payment_type === 'one_time') {
            $paypal          = new FED_PayPal\FED_PayPal();
            $details         = fed_fetch_table_row_by_id(BC_FED_PAY_PAYMENT_PLAN_TABLE, (int)$plan_id);
            $payment_details = $this->format_payment($details);
            $status          = $paypal->payment_start($payment_details);
        }

        if ($payment_type === 'subscription') {
            $plan  = new FED_PayPal\FED_PayPal();
            $table = $plan->get_plan_by_id($plan_id);
            bcdump($table);
        }


    }

    /**
     * @param $details
     *
     * @return array
     */
    private function format_payment($details)
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


//            $sub_total_array[$random] = $price_quantity + $item_tax_value;
//            $sub_total_array[$random]['tax'] = $item_tax_value;
//            $sub_total_array[$random]['total'] = $price_quantity;
        }

//        bcdump($sub_total_array);

        $shipping_discount       = isset($amount['details']['shipping_discount']) ? (float)fed_sanitize_text_field($amount['details']['shipping_discount']) : 0;
        $shipping_discount_type  = isset($amount['details']['shipping_discount_type']) ? fed_sanitize_text_field($amount['details']['shipping_discount_type']) : 'fixed';
        $shipping_discount_value = $this->get_tax($shipping_discount_type, $sub_total, $shipping_discount);

        $insurance       = isset($amount['details']['insurance']) ? (float)fed_sanitize_text_field($amount['details']['insurance']) : 0;
        $insurance_type  = isset($amount['details']['insurance_type']) ? fed_sanitize_text_field($amount['details']['insurance_type']) : 'fixed';
        $insurance_value = $this->get_tax($insurance_type, $sub_total, $insurance);

        $tax       = isset($amount['details']['tax']) ? (float)($amount['details']['tax']) : 0;
        $tax_type  = isset($amount['details']['tax_type']) ? fed_sanitize_text_field($amount['details']['tax_type']) : 'fixed';
        $tax_value = (float)$this->get_tax($tax_type, $sub_total, $tax);


        $gift_wrap    = isset($amount['details']['gift_wrap']) ? (float)($amount['details']['gift_wrap']) : 0;
        $shipping     = isset($amount['details']['shipping']) ? (float)($amount['details']['shipping']) : 0;
        $handling_fee = isset($amount['details']['handling_fee']) ? (float)($amount['details']['handling_fee']) : 0;

        $total = $sub_total + $shipping + $tax_value + $handling_fee - $shipping_discount_value + $insurance_value  + $gift_wrap ;

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
                                                        'gift_wrap'         => $gift_wrap,
                                                ),
                                        ),
                                        'description'    => isset($details['description']) ? fed_sanitize_text_field($details['description']) : '',
                                        'invoice_number' => current_time('YmdHis').'_'.fed_get_random_string(10),
                                        'reference_id'   => isset($details['reference_id']) ? fed_sanitize_text_field($details['reference_id']) : '',
                                        'note_to_payee'  => isset($details['note_to_payee']) ? fed_sanitize_text_field($details['note_to_payee']) : '',
                                        'purchase_order' => isset($details['purchase_order']) ? fed_sanitize_text_field($details['purchase_order']) : '',
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