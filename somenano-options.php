<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( plugin_dir_path( __FILE__ ) . 'somenano-brainblocks.php' );
require_once( plugin_dir_path( __FILE__ ) . 'somenano-defaults.php' );

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'SomeNano for Wordpress Settings', 
            'SomeNano', 
            'manage_options', 
            'somenano-settings', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'somenano_options' );
        ?>
        <div class="wrap">
            <h1>SomeNano for Wordpress Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'somenano-settings' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'my_option_group', // Option group
            'somenano_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'settings_section_howto',
            'How SomeNano for Wordpress Works',
            array( $this, 'print_howto_info' ),
            'somenano-settings'
        );

        add_settings_section(
            'setting_section_paywall', // ID
            'Default Paywall Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'somenano-settings' // Page
        );

        add_settings_field(
            'default_paywall_account', // ID
            'Nano Account for Paywall', // Title 
            array( $this, 'default_paywall_account_callback' ), // Callback
            'somenano-settings', // Page
            'setting_section_paywall' // Section           
        );

        add_settings_field(
            'default_paywall_currency', 
            'Paywall Currency', 
            array( $this, 'default_paywall_currency_callback' ), 
            'somenano-settings', 
            'setting_section_paywall'
        );

        add_settings_field(
            'default_paywall_amount',
            'Paywall Amount',
            array( $this, 'default_paywall_amount_callback' ),
            'somenano-settings',
            'setting_section_paywall'
        );

        add_settings_field(
            'default_paywall_preface',
            'Paywall Preface',
            array( $this, 'default_paywall_preface_callback' ),
            'somenano-settings',
            'setting_section_paywall'
        );

        add_settings_field(
            'default_paywall_paid_note',
            'Paywall Paid Note',
            array( $this, 'default_paywall_paid_note_callback' ),
            'somenano-settings',
            'setting_section_paywall'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        // print_r( $input );
        // die();
        $new_input = array();

        if( isset( $input['default_paywall_account'] ) &&
            preg_match( '/^(xrb|nano)_[a-z0-9]{60}$/i', $input['default_paywall_account'] ) ) {

            $new_input['default_paywall_account'] = sanitize_text_field( $input['default_paywall_account'] );
        }

        if( isset( $input['default_paywall_currency'] ) && in_array( $input['default_paywall_currency'], bb_currencies() ) ) {
            $new_input['default_paywall_currency'] = sanitize_text_field( $input['default_paywall_currency'] );
        }

        if( isset( $input['default_paywall_amount'] ) && is_numeric( $input['default_paywall_amount'] ) ) {
            if ( substr($input['default_paywall_amount'], 0, 1) == '.' ) {
                $input['default_paywall_amount'] = '0' . $input['default_paywall_amount'];
                error_log($input['default_paywall_amount']);
            }
            $new_input['default_paywall_amount'] = sanitize_text_field( $input['default_paywall_amount'] );
        }

        if( isset( $input['default_paywall_preface'] ) ) {
            $new_input['default_paywall_preface'] = $input['default_paywall_preface'];
        }

        if( isset( $input['default_paywall_paid_note'] ) ) {
            $new_input['default_paywall_paid_note'] = $input['default_paywall_paid_note'];
        }

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_howto_info()
    {
        ?>
<p>So you want to accept <a href="https://nano.org" target="_new">Nano</a> cryptocurrency on Wordpress... let's get started!</p>
<p>Please consider making a <a href="https://nanocrawler.cc/explorer/account/xrb_3nahhuscs9ott91ynow4czt96nmwk8ugsw4k6acki36b4n5fcryuuk96mfrm/history" target="_new">donation</a>. This is a free plugin provided to help you make money off your hard work.</p>
<h3>Paywall HowTo</h3>
<p>Type the shortcode [somenano_paywall] anywhere in a post or page.  All content after the shortcode will not be shown to the user until the user sends payment.  After the payment is sent, the paywall will be gone forever for a user that was logged in to the site, and for users not logged in, will be gone as long as the saved cookie remains in their browser.</p>
<p>The following attributes can be given in the shortcode and override the default values on this settings page</p>
<ul>
    <li>account: Nano account to receive payment</li>
    <li>currency: Type of currency for amount.
    <li>amount: The amount in set currency to charge for the paywall.  Will be converted to Nano for payment.</li>
    <li>preface: Text to show on paywall to inform the user what to do.</li>
    <li>paid_note: Text that will be shown instead of the paywall after a payment is made.</li>
</li>
<p>Example: [somenano_paywall currency="usd" amount="0.10"]
        <?php
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print '<p>This is what your currently saved Paywall looks like:';

        $account = isset( $this->options['default_paywall_account'] ) ? esc_attr( $this->options['default_paywall_account']) : '';
        $preface = isset( $this->options['default_paywall_preface'] ) ? esc_attr( $this->options['default_paywall_preface']) : somenano_default('paywall_preface');
        $currency = isset( $this->options['default_paywall_currency'] ) ? esc_attr( $this->options['default_paywall_currency']) : somenano_default('paywall_curreny');
        $amount = isset( $this->options['default_paywall_amount'] ) ? esc_attr( $this->options['default_paywall_amount']) : somenano_default('paywall_amount');
        $paid_note = isset( $this->options['default_paywall_paid_note'] ) ? esc_attr( $this->options['default_paywall_paid_note']) : somenano_default('paywall_paid_note');

        print '<div id="somenano-paywall">';
        if ( $account == '' ) print '<br>** SomeNano Error: Nano account required for paywall **';
        else print $preface;
        print bb_button();
        print '</div>';
        print bb_script();
        print bb_render(
            $currency,
            $amount,
            $account,
            ''
        );
        print '</p>';
        print '<p>And this is what your currently saved Paywall looks like after a payment is made:';
        print '<div id="somenano-paywall">';
        print $paid_note;
        print '</div>';
        print '</p>';

        print( "<br><b>Note:</b> The address shown in the Paywall will not be your saved account. It is an intermediary account that <a href='https://brainblocks.io' target='_new'>BrainBlocks</a> uses to process payments. After the payment is received in full, BrainBlocks will forward the payment to your account");

    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function default_paywall_account_callback()
    {
        printf(
            '<input type="text" id="default_paywall_account" name="somenano_options[default_paywall_account]" value="%s" placeholder="nano_1s0m3nan0..."/>',
            isset( $this->options['default_paywall_account'] ) ? esc_attr( $this->options['default_paywall_account']) : ''
        );
        print( "<br>Create free Nano account at <a href='https://nanovault.io' target='_blank'>https://nanovault.io</a>" );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function default_paywall_currency_callback()
    {

        $value = isset( $this->options['default_paywall_currency'] ) ? esc_attr( $this->options['default_paywall_currency']) : somenano_default('paywall_curreny');
        print( "<select id='default_paywall_currency' name='somenano_options[default_paywall_currency]'>" );
        foreach ( bb_currencies() as $cur ) {
            printf( "<option value='$cur' %s>$cur</option>", $cur == $value ? "selected" : "" );
        }
        print( "</select>" );

        print( "<br>Payments are in Nano [<b>1,000,000 rai</b> (in the dropdown) is equal to <b>1 Nano</b>]. The value of the payment will be converted from this selected currency at the time the paywall loads" );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function default_paywall_amount_callback()
    {
        printf(
            '<input type="text" id="default_paywall_amount" name="somenano_options[default_paywall_amount]" value="%s" placeholder="0.1"/>',
            isset( $this->options['default_paywall_amount'] ) ? esc_attr( $this->options['default_paywall_amount']) : somenano_default('paywall_amount')
        );
        print( "<br>This is the amount in <b>Paywall Currency</b> that be paid in Nano for a user to go through the Paywall." );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function default_paywall_preface_callback()
    {
        printf(
            '<textarea rows="4" cols="50" id="default_paywall_preface" name="somenano_options[default_paywall_preface]">%s</textarea>',
            isset( $this->options['default_paywall_preface'] ) ? esc_attr( $this->options['default_paywall_preface']) : somenano_default('paywall_preface')
        );
        print( "<br>HTML tags allowed, this message will be shown right above the Paywall button" );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function default_paywall_paid_note_callback()
    {
        printf(
            '<textarea rows="4" cols="50" id="default_paywall_paid_note" name="somenano_options[default_paywall_paid_note]">%s</textarea>',
            isset( $this->options['default_paywall_paid_note'] ) ? esc_attr( $this->options['default_paywall_paid_note']) : somenano_default('paywall_paid_note')
        );
        print( "<br>HTML tags allowed, this message will replace the paywall after payment is made.  It can be blank if that's what you'd like." );
    }
}

if( is_admin() )
    $my_settings_page = new MySettingsPage();