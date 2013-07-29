<?php
/*
Plugin Name: Mytory Borrowing Cashbook
Plugin URI:
Description: Borrow or borrowed money is tiny of overall money stream. But this is headache. This plugin is simple cashbook that keep track of borrow/borrowed money.
Author: mytory
Version: 0.9
Author URI: http://mytory.net
*/

/**
 * set language text domain.
 */
function mbc_init () {
    load_plugin_textdomain('mytory-borrowing-cashbook', FALSE, dirname(plugin_basename(__FILE__)) . '/lang');
}
add_action('plugins_loaded', 'mbc_init');

function mbc_custom_post_type(){
    $args = array(
        'label' => __('borrowing'),
    );
    register_post_type('mytory_borrowing', $args);
    register_taxonomy('person', 'mytory_borrowing');
    register_taxonomy('borrowing_type', 'mytory_borrowing');
}
add_action( 'init', 'mbc_custom_post_type' );

function mbc_enqueue_scripts(){
    wp_enqueue_script('jquery-form');
    wp_register_script('mbc-js', plugin_dir_url(__FILE__) . 'js.js', array('jquery', 'jquery-form'), false, true);
    wp_localize_script('mbc-js', 'mytory_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    wp_enqueue_script('mbc-js');
}
add_action('wp_enqueue_scripts', 'mbc_enqueue_scripts');

/**
 * Insert page to use as cashbook page.
 */
function mbc_activation(){
    $postarr = array(
        'post_title' => 'Mytory Borrowing Cashbook Page',
        'post_content' => '[mytory-borrowing-cashbook]',
        'post_status' => 'private',
        'post_type' => 'page',
    );
    $post_id = wp_insert_post($postarr);
    if($post_id){
        update_option('mytory-borrowing-cashbook-page-id', $post_id);
    }
}
register_activation_hook( __FILE__, 'mbc_activation');

/**
 * Print cashbook page html.
 * @return string
 */
function mbc_print_page(){
    return include(dirname(__FILE__) . '/page-cashbook-main.php');
}
add_shortcode('mytory-borrowing-cashbook', 'mbc_print_page');

/**
 * return saved cashbook page ID
 * @return mixed|void
 */
function mbc_get_page_id(){
    return get_option('mytory-borrowing-cashbook-page-id');
}

//================ ajax handle ================//

/**
 * save one borrowing cash
 */
function mbc_save_one_borrowing(){
    $postarr = array(
        'post_title' => "{$_POST['person']} | {$_POST['date']} | {$_POST['cash']}",
        'post_type' => 'mytory_borrowing',
        'post_status' => 'private',
        'tax_input' => array(
            'person' => array($_POST['person']),
            'borrowing_type' => array($_POST['borrowing_type']),
        ),
    );
    $post_id = wp_insert_post($postarr);

    $key_arr = array('cash', 'date', 'desc');
    $is_error = false;
    foreach ($key_arr as $key) {
        if(empty($_POST[$key])){
            continue;
        }
        if( ! update_post_meta($post_id, $key, $_POST[$key])){
            $is_error = true;
        }
    }

    if($is_error){
        echo 0;
    }else{
        echo 1;
    }
    die();
}


function mbc_must_login() {
    echo "You must log in";
    die();
}
add_action("wp_ajax_save_one_borrowing", "mbc_save_one_borrowing");
add_action("wp_ajax_nopriv_save_one_borrowing", "mbc_must_login");

/**
 * print basic borrowing list
 */
function mbc_print_borrowing_list(){
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'mytory_borrowing',
        'meta_key' => 'date',
        'orderby' => 'meta_value',
        'order' => 'DESC',
    );
    if(isset($_POST['person']) AND ! empty($_POST['person'])){
        $args['person'] = $_POST['person'];
    }
    $query = new WP_Query($args);
    $posts = $query->posts;
    $borrowing_arr = mbc_get_borrowing_arr($posts);
    $total_to_others = mbc_get_total_to_others($borrowing_arr);
    $total_from_others = mbc_get_total_from_others($borrowing_arr);
    $by_person = mbc_get_borrowing_by_person($borrowing_arr);
    ?>
    <div class="mbc-summary">
        <h2><?_e('Total', 'mytory-borrowing-cashbook')?></h2>
        <ul>
            <li><?_e('Total to others', 'mytory-borrowing-cashbook')?> : <?=number_format($total_to_others)?></li>
            <li><?_e('Total from others', 'mytory-borrowing-cashbook')?> : <?=number_format($total_from_others)?></li>
        </ul>
        <h2><?_e('By person', 'mytory-borrowing-cashbook')?></h2>
        <ul>
            <?foreach ($by_person as $person => $b_arr_by_person) {?>
                <?
                $to_person = mbc_get_total_to_others($b_arr_by_person);
                $from_person = mbc_get_total_from_others($b_arr_by_person);
                ?>
                <? if($to_person){ ?>
                    <li>
                        <?=str_replace('$person', $person, __('To $person', 'mytory-borrowing-cashbook'))?> :
                        <?=number_format($to_person)?>
                    </li>
                <?}?>
                <? if($from_person){ ?>
                    <li>
                        <?=str_replace('$person', $person, __('From $person', 'mytory-borrowing-cashbook'))?> :
                        <?=number_format($from_person)?>
                    </li>
                <?}?>
            <?}?>
        </ul>
    </div>
    <table class="mbc-borrowing-list">
        <thead>
        <tr>
            <th><?_e('Type', 'mytory-borrowing-cashbook')?></th>
            <th><?_e('Date', 'mytory-borrowing-cashbook')?></th>
            <th><?_e('Person', 'mytory-borrowing-cashbook')?></th>
            <th><?_e('Cash', 'mytory-borrowing-cashbook')?></th>
            <th><?_e('Description', 'mytory-borrowing-cashbook')?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($borrowing_arr as $b) {
            ?>
            <tr data-post-id="<?=$b['post_id']?>">
                <td><?= _e($b['borrowing_type'], 'mytory-borrowing-cashbook')?></td>
                <td><?= $b['date'] ?></td>
                <td><?= $b['person'] ?></td>
                <td class="mbc-cash"><?= number_format($b['cash']) ?></td>
                <td><?= $b['desc'] ?></td>
                <td><input type="button" onclick="mytory_borrowing.delete_one_borrowing(<?=$b['post_id']?>)" value="<?_e('X')?>"/></td>
            </tr>
        <? } ?>
        </tbody>
    </table>
    <?
    die();
}

add_action("wp_ajax_print_borrowing_list", "mbc_print_borrowing_list");
add_action("wp_ajax_nopriv_print_borrowing_list", "mbc_must_login");

function mbc_get_borrowing_type($post_id){
    $temp = wp_get_post_terms($post_id, 'borrowing_type');
    $borrowing_type = $temp[0]->name;
    return $borrowing_type;
}

function mbc_get_person($post_id){
    $temp = wp_get_post_terms($post_id, 'person');
    $person = $temp[0]->name;
    return $person;
}

function mbc_delete_one_borrowing(){
    if(wp_delete_post($_POST['post_id'])){
        echo 1;
    }else{
        echo 0;
    }
    die();
}

add_action("wp_ajax_delete_one_borrowing", "mbc_delete_one_borrowing");
add_action("wp_ajax_nopriv_delete_one_borrowing", "mbc_must_login");

function mbc_get_borrowing_arr($posts){
    $borrowing_arr = array();
    foreach ($posts as $p) {
        $borrowing_type = mbc_get_borrowing_type($p->ID);
        $person = mbc_get_person($p->ID);
        $borrowing_arr[] = array(
            'post_id' => $p->ID,
            'borrowing_type' => $borrowing_type,
            'person' => $person,
            'cash' => get_post_meta($p->ID, 'cash', true),
            'date' => get_post_meta($p->ID, 'date', true),
            'desc' => get_post_meta($p->ID, 'desc', true),
        );
    }
    return $borrowing_arr;
}

function mbc_get_borrowing_by_person($borrowing_arr){
    $by_person = array();
    foreach ($borrowing_arr as $b) {
        $by_person[$b['person']][] = $b;
    }
    return $by_person;
}

/**
 * Get cash that should return to others.
 * 총 갚아야 할 돈을 구한다.
 * @param $borrowing_arr
 * @return int
 */
function mbc_get_total_to_others($borrowing_arr){
    $total_return_to = mbc_get_total('return to', $borrowing_arr);
    $total_borrow_from = mbc_get_total('borrow from', $borrowing_arr);
    return $total_borrow_from - $total_return_to;
}

/**
 * Get cash that should get.
 * 총 받아야할 돈을 구한다.
 * @param $borrowing_arr
 * @return int
 */
function mbc_get_total_from_others($borrowing_arr){
    $total_borrow_to = mbc_get_total('borrow to', $borrowing_arr);
    $total_return_from = mbc_get_total('return from', $borrowing_arr);
    return $total_borrow_to - $total_return_from;
}

function mbc_get_total($borrowing_type, $borrowing_arr){
    $total = 0;
    foreach ($borrowing_arr as $b) {
        if($b['borrowing_type'] == $borrowing_type){
            $total += $b['cash'];
        }
    }
    return $total;
}

function mbc_get_persons(){
    $terms = get_terms('person');
    $persons = array();
    foreach ($terms as $t) {
        $persons[] = $t->name;
    }
    return $persons;
}