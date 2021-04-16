<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit, if accessed directly

$chartid  = $atts['chartid'];
$hc_title = $atts['title'];
$prefix = get_post_meta( $chartid, 'default_prefix', true );
$currency = get_post_meta( $chartid, 'default_currency', true );

if ( '' === $chartid && '' === $currency ) {
	?>
    <p class="hc-highchart-render-error"><?php _e( 'The chart ID and currency details are missing !!', 'google-highcharts' ); ?></p>
	<?php
} elseif ( '' === $chartid ) {
	?>
    <p class="hc-highchart-render-error"><?php _e( 'The chart ID is missing !!', 'google-highcharts' ); ?></p>
	<?php
} elseif ( '' === $currency ) {
	?>
    <p class="hc-highchart-render-error"><?php _e( 'The currency details are missing !!', 'google-highcharts' ); ?></p>
	<?php
} else {
	$is_chart_id_numeric = is_numeric( $chartid );
	if ( false === $is_chart_id_numeric ) {
		?>
        <p class="hc-highchart-render-error"><?php _e( 'Chart ID must be numeric !!', 'google-highcharts' ); ?></p>
		<?php
	} else {
		$valid_currencies   = array( 'USD', 'EUR', 'GBP', 'YEN', 'CNY' );
		$allowed_currencies = implode( ',', $valid_currencies );
		if ( ! in_array( $currency, $valid_currencies, true ) ) {
			?>
            <p class="hc-highchart-render-error"><?php echo sprintf( __( 'Invalid currency !!%2$s Currencies allowed: %1$s', 'google-highcharts' ), "<strong>{$allowed_currencies}</strong>", "<br />" ); ?></p>
			<?php
		} else {
			$years_arr  = get_post_meta( $chartid, 'year_arr', true );
			$number_arr = get_post_meta( $chartid, 'number_arr', true );

			$value_prefix = '';

			if ( 'USD' === $currency ) {
				$currency_arr = get_post_meta( $chartid, 'usd_arr', true );
				$value_prefix = '$';
			} elseif ( 'EUR' === $currency ) {
				$currency_arr = get_post_meta( $chartid, 'euro_arr', true );
				$value_prefix = '€';
			} elseif ( 'GBP' === $currency ) {
				$currency_arr = get_post_meta( $chartid, 'gbp_arr', true );
				$value_prefix = '£';
			} elseif ( 'YEN' === $currency ) {
				$currency_arr = get_post_meta( $chartid, 'yen_arr', true );
				$value_prefix = '円';
			} elseif ( 'CNY' === $currency ) {
				$currency_arr = get_post_meta( $chartid, 'cny_arr', true );
				$value_prefix = '¥';
			}
			$cpt         = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
			$extra_class = ( ! empty( $cpt ) && 'highchart-shortcode' === $cpt ) ? 'hc-admin-view' : '';
			// ob_start();
			?>
            <div id="highchart-container-<?php echo $chartid; ?>" class="<?php echo $extra_class; ?>"></div>
            <script src="<?php echo HC_PLUGIN_URL . 'admin/js/highcharts.js'; ?>"></script>
			<?php if ( is_user_logged_in() ) { ?>
                <script src="<?php echo HC_PLUGIN_URL . 'admin/js/exporting.js'; ?>"></script>
			<?php } else {
				$login_url = home_url( '/login/' );
				$reg_url   = home_url( '/register/' );
				echo sprintf ( __( 'Download or View Data, please <a href="%s">sign in</a> or <a
                        href="%s">register</a> as a user.','google-highcharts'), $login_url, $reg_url ); ?>
			<?php } ?>
            <script src="<?php echo HC_PLUGIN_URL . 'admin/js/export-data.js'; ?>"></script>
            <script type="text/javascript">
                var years_arr = '<?php echo json_encode( $years_arr );?>';
                var number_arr = '<?php echo json_encode( $number_arr );?>';
                var currency_arr = '<?php echo json_encode( $currency_arr );?>';
                var currency = '<?php echo $currency;?>';
                var prefix = '<?php echo $prefix;?>';
                var hc_title = '<?php echo $hc_title;?>';
                var chartid = '<?php echo $chartid;?>';
                var value_prefix = '<?php echo $value_prefix;?>';

                years_arr = JSON.parse(years_arr);
                number_arr = JSON.parse(number_arr);
                currency_arr = JSON.parse(currency_arr);

                Highcharts.chart(
                    'highchart-container-' + chartid, {
                        chart: {
                            zoomType: 'xy'
                        },
                        title: {
                            text: hc_title
                        },
                        subtitle: {
                            text: ''
                        },
                        xAxis: [{
                            categories: years_arr,
                            crosshair: true
                        }],
                        yAxis: [{
                            title: {
                                text: 'Value of Transactions (in ' + prefix + currency + ')',
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            },
                            labels: {
                                format: '{value}',
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            },
                            opposite: true
                        }, {
                            labels: {
                                format: '{value}',
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            },
                            title: {
                                text: 'Number of Transactions',
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            }
                        }],
                        tooltip: {
                            shared: true
                        },
                        /*legend: {
							layout: 'vertical',
							align: 'left',
							x: 120,
							verticalAlign: 'top',
							y: 500,
							floating: true,
							backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFF'
						},*/
                        series: [{
                            name: 'Number',
                            type: 'column',
                            yAxis: 1,
                            data: number_arr,
                            tooltip: {
                                valueSuffix: ''
                            }

                        }, {
                            name: 'Value',
                            type: 'spline',
                            data: currency_arr,
                            tooltip: {
                                valuePrefix: value_prefix
                            }
                        }]
                    });
            </script>
			<?php
		}
	}
}