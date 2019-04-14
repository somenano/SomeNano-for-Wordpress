<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// REF: https://brainblocks.io/

function bb_currencies()
{
    return array("aed", "afn", "all", "amd", "ang", "aoa", "ars", "aud", "awg", "azn", "bam", "bbd", "bch", "bdt", "bgn", "bhd", "bif", "bmd", "bnd", "bob", "brl", "bsd", "btc", "btn", "bwp", "bzd", "cad", "cdf", "chf", "clf", "clp", "cny", "cop", "crc", "cup", "cve", "czk", "djf", "dkk", "dop", "dzd", "egp", "etb", "eur", "fjd", "fkp", "gbp", "gel", "ghs", "gip", "gmd", "gnf", "gtq", "gyd", "hkd", "hnl", "hrk", "htg", "huf", "idr", "ils", "inr", "iqd", "irr", "isk", "jep", "jmd", "jod", "jpy", "kes", "kgs", "khr", "kmf", "kpw", "krw", "kwd", "kyd", "kzt", "lak", "lbp", "lkr", "lrd", "lsl", "lyd", "mad", "mdl", "mga", "mkd", "mmk", "mnt", "mop", "mru", "mur", "mvr", "mwk", "mxn", "myr", "mzn", "nad", "ngn", "nio", "nok", "npr", "nzd", "omr", "pab", "pen", "pgk", "php", "pkr", "pln", "pyg", "qar", "rai", "ron", "rsd", "rub", "rwf", "sar", "sbd", "scr", "sdg", "sek", "sgd", "shp", "sll", "sos", "srd", "stn", "svc", "syp", "szl", "thb", "tjs", "tmt", "tnd", "top", "try", "ttd", "twd", "tzs", "uah", "ugx", "usd", "uyu", "uzs", "vef", "vnd", "vuv", "wst", "xaf", "xag", "xau", "xcd", "xof", "xpf", "yer", "zar", "zmw", "zwl");
}

function bb_button()
{
    return '<div id="nano-button"></div>';
}

function bb_script()
{
    return '<script src="https://brainblocks.io/brainblocks.min.js"></script>';
}

function bb_render( $currency, $amount, $destination, $onPayment )
{
    return '<script type="text/javascript">
            brainblocks.Button.render({

                payment: {
                    currency: "' . $currency . '",
                    amount: "' . $amount . '",
                    destination: "' . $destination . '"
                },

                onPayment: function(data) {
                    console.log(data);
                    if (data.status == "success") {
                        ' . $onPayment . '
                    }
                }
            }, "#nano-button");
        </script>';
}

function bb_token( $token )
{
    /*
    {
        "type": "nano",
        "token": "ZXlKaGJHY2lPaUpJVXpJMU5pSXNJblI1Y0NJNklrcFhWQ0o5LmV5SjBlWEJsSWpvaWJtRnVieUlzSW1sa0lqb2lPVGRpWlRRME1HWXRORGsxTlMwMFlUWTBMV0ZsT0RJdE1ERXpPVFV5WkRsbU9HUXpJaXdpYVdGMElqb3hOVFUxTVRBNE5UVTJMQ0psZUhBaU9qRTFOVFV4TVRJeE5UWjkuRFh4S0h0UmFSd0NGRVZJbElrZjlOZTBDZmxYdTNoaVI0TUF6T0w0WHl4RQ",
        "destination": "xrb_1nanoteiu8euwzrgqnn79c1fhpkeuzi4b4ptogoserckbxkw15dma6dg5hb5",
        "currency": "usd",
        "amount": "0.25",
        "amount_rai": 155000,
        "received_rai": 155000,
        "fulfilled": true,
        "send_block": "AAD4A2698C85BA4170E81C62A6EA5FF41EAA5A28E10F91C1F71162281D33976C",
        "sender": "xrb_1nanoteiu8euwzrgqnn79c1fhpkeuzi4b4ptogoserckbxkw15dma6dg5hb5"
    }
    */
    $url = "https://api.brainblocks.io/api/session/" . $token . "/verify";
    $response = wp_remote_get( esc_url_raw( $url ), array('blocking' => true, 'timeout' => 30) );
    return json_decode( wp_remote_retrieve_body( $response ), true );
}

?>