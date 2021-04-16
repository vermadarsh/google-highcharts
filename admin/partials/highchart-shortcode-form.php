<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit, if accessed directly

$google_sheet_submit = filter_input(INPUT_POST, 'googlesheet-submit', FILTER_SANITIZE_STRING);
if (isset($google_sheet_submit) && wp_verify_nonce($_POST['hc-googlesheet-submit-nonce'], 'hc-googlesheet-submit')) {
    if (!empty($_FILES['hc-googlesheet-file']['name'])) {
        $target_file = HC_UPLOADS_PATH . '/' . basename($_FILES['hc-googlesheet-file']['name']);
        if (move_uploaded_file($_FILES['hc-googlesheet-file']['tmp_name'], $target_file)) {
            $sheet_path = HC_UPLOADS_PATH . '/' . basename($_FILES['hc-googlesheet-file']['name']);
            $excel_reader = HC_PLUGIN_PATH . 'includes/excel-reader/simplexlsx.class.php';

            if (file_exists($excel_reader)) {
                include $excel_reader;
                $xlsx = @(new SimpleXLSX($sheet_path));
                $sheet_data =  $xlsx->rows();
                $year_arr = $number_arr = $usd_arr = $euro_arr = $gbp_arr = $yen_arr = $cny_arr = array();
                if (!empty($sheet_data)) {
                    foreach ($sheet_data as $t => $data) {
                        $data = array_filter( $data );
                        if( empty( $data ) ) {
                            unset( $sheet_data[ $t ] );
                        }
                    }
                    foreach ($sheet_data as $t => $data) {
                        $usd_key = $eur_key = $gbp_key = $yen_key = $cny_key = '';
                        $currency_keys = array_filter($sheet_data[1]);

                        if (!empty($currency_keys)) {
                            foreach ($currency_keys as $k => $c_key) {

                                $c_key = preg_replace('/[\x00-\x1F\x7F]/', '', $c_key);

                                if (false !== stripos($c_key, 'USD')) {
                                    $usd_key = $k;
                                }
                                if (false !== stripos($c_key, 'EUR')) {
                                    $eur_key = $k;
                                }
                                if (false !== stripos($c_key, 'GBP')) {
                                    $gbp_key = $k;
                                }
                                if (false !== stripos($c_key, 'YEN')) {
                                    $yen_key = $k;
                                }
                                if (false !== stripos($c_key, 'CNY')) {
                                    $cny_key = $k;
                                }
                            }
                        }
                        if (0 == $t || 1 == $t) {
                            continue;
                        }
                        $year = preg_replace('/[\x00-\x1F\x7F]/', '', $data[0]);

                        $year_arr[] = $year;
                        $number_arr[] = (int)$data[1];
                        $usd_arr[] = (float)$data[$usd_key];
                        $euro_arr[] = (float)$data[$eur_key];
                        $gbp_arr[] = (float)$data[$gbp_key];
                        $yen_arr[] = (float)$data[$yen_key];
                        $cny_arr[] = (float)$data[$cny_key];

                    }

                }
                $chart_input_title = sanitize_text_field($_POST['hc-highchart-title']);
                $chart_prefix = sanitize_text_field($_POST['hc-prefix']);
                $chart_currency = sanitize_text_field($_POST['hc-currency']);
                $chart_title = 'Chart - ' . $chart_input_title;
                $args = array(
                    'post_type' => 'highchart-shortcode',
                    'post_title' => $chart_title,
                    'post_status' => 'publish',
                );

                $chart_id = wp_insert_post($args);
                update_post_meta($chart_id, 'year_arr', $year_arr);
                update_post_meta($chart_id, 'number_arr', $number_arr);
                update_post_meta($chart_id, 'usd_arr', $usd_arr);
                update_post_meta($chart_id, 'euro_arr', $euro_arr);
                update_post_meta($chart_id, 'gbp_arr', $gbp_arr);
                update_post_meta($chart_id, 'yen_arr', $yen_arr);
                update_post_meta($chart_id, 'cny_arr', $cny_arr);
                update_post_meta($chart_id, 'chart_title', $chart_input_title);
                update_post_meta($chart_id, 'default_prefix', $chart_prefix);
                update_post_meta($chart_id, 'default_currency', $chart_currency);
                ?>
                <div class='notice updated' id='message'>
                    <p><?php echo sprintf(__('Highchart created with title: %1$s. %2$s.', 'google-highcharts'), "<strong>{$chart_title}</strong>", "<a href='" . get_permalink( $chart_id ) . "' target='_blank' title='".$chart_title."'>View Chart</a>"); ?></p>
                </div>
                <?php
            } else {
                ?>
                <div class='notice error' id='message'>
                    <p><?php _e('File reader library missing.', 'google-highcharts'); ?></p>
                </div>
                <?php
            }
        } else {
            ?>
            <div class='notice error' id='message'>
                <p><?php _e('File not uploaded due to some error !! Please try again.', 'google-highcharts'); ?></p>
            </div>
            <?php
        }
    }
}
?>
<div class="wrap">
    <h2><?php _e('Create Highchart Shortcode', 'google-highcharts'); ?></h2>
    <form class="hc-generate-hghchart-shortcode" action="" method="POST" enctype="multipart/form-data">

        <table class="form-table">
            <tbody>
            <!-- HIGHCHART TITLE -->
            <tr>
                <th scope="row"><label
                            for="hc-highchart-title"><?php _e('Highchart Title', 'google-highcharts'); ?></label>
                </th>
                <td>
                    <input type="text" name="hc-highchart-title" class="regular-text"
                           placeholder="<?php _e('Title', 'google-highcharts'); ?>" required>
                    <p class="description"><?php _e('Highchart Title is entered here.', 'google-highcharts'); ?></p>
                </td>
            </tr>

            <!-- XLS/XLSX FILE -->
            <tr>
                <th scope="row"><label
                            for="hc-googlesheet-file"><?php _e('.xlsx File', 'google-highcharts'); ?></label>
                </th>
                <td>
                    <p class="hc-file-err"></p>
                    <input name="hc-googlesheet-file" type="file" id="hc-googlesheet-file" required>
                    <p class="description"><?php _e('Google Sheet File.', 'google-highcharts'); ?></p>
                </td>
            </tr>

            <!--Mil or BIL-->
            <tr>
                <th scope="row"><label
                            for="hc-googlesheet-prefix"><?php _e('Prefix', 'google-highcharts'); ?></label>
                </th>
                <td>
                    <select name="hc-prefix" required="required">
                        <option value=""><?php _e( '--Select Prefix--', 'google-highcharts' );?></option>
                        <option value="mil.">mil.</option>
                        <option value="bil.">bil.</option>
                    </select>
                    <p class="description"><?php _e('Prefix Type, mil. or bil.', 'google-highcharts'); ?></p>
                </td>
            </tr>


            <!--Currency Option-->
            <tr>
                <th scope="row"><label
                            for="hc-googlesheet-currency"><?php _e('Currency', 'google-highcharts'); ?></label>
                </th>
                <td>
                    <select name="hc-currency" required="required">
                        <option value=""><?php _e( '--Select Currency--', 'google-highcharts' );?></option>
                        <option value="EUR">EUR</option>
                        <option value="USD">USD</option>
                        <option value="GBP">GBP</option>
                        <option value="YEN">YEN</option>
                        <option value="CNY">CNY</option>
                    </select>
                    <p class="description"><?php _e('Currency for the highchart.', 'google-highcharts'); ?></p>
                </td>
            </tr>

            </tbody>
        </table>
        <p class="submit">
            <?php wp_nonce_field('hc-googlesheet-submit', 'hc-googlesheet-submit-nonce'); ?>
            <input type="submit" name="googlesheet-submit" value="<?php esc_html_e('Submit', 'google-highcharts'); ?>"
                   class="button button-primary">
        </p>
    </form>
</div>