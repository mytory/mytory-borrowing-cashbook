<?
if( ! empty($_POST)){
    update_option('mytory-borrowing-cashbook-currency', $_POST['mbc-currency']);
    ?>
    <div class="updated">
        <p><?_e('Option saved!', 'mytory-borrowing-cashbook')?></p>
    </div>
    <?
}
?>

<div class='wrap'>
    <div class="icon32" id="icon-options-general"><br></div>
    <h2><?php _e('Mytory Borrowing Cashbook', 'mytory-borrowing-cashbook') ?></h2>

    <p>
        <a href="<?=home_url('?page_id=' . get_option('mytory-borrowing-cashbook-page-id'))?>">
        <?_e('Go to Cashbook Page.', 'mytory-borrowing-cashbook')?>
        </a>
    </p>

    <form method="post">
        <table class="form-table">
            <tr>
                <th>
                    <label for="mbc-currency">
                        <?_e('currency', 'mytory-borrowing-cashbook')?> :
                    </label>
                </th>
                <td>
                    <input type="text" name="mbc-currency" id="mbc-currency" value="<?=get_option('mytory-borrowing-cashbook-currency')?>"/>
                </td>
            </tr>
        </table>
    </form>
    <p class="submit">
        <input class="button button-primary" type="submit" value="<? _e('Save') ?>"/>
    </p>
</div>