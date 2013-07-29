<script type="text/javascript">
    var ajax_action_url = '<?= admin_url('admin-ajax.php') ?>';
</script>
<style type="text/css">
    .mbc-borrowing-list-container {
        margin-top: 2em;
    }
    .mbc-cash {
        text-align: right;
        padding-right: 1em!important;
    }
</style>

<form class="mbc-basic-form" action="<?= admin_url('admin-ajax.php') ?>" method="post">
    <input type="hidden" name="action" value="save_one_borrowing"/>
    <table>
        <colgroup>
            <col width="20%">
        </colgroup>
        <tr>
            <th><?_e('Type', 'mytory-borrowing-cashbook')?></th>
            <td>
                <input type="radio" name="borrowing_type" id="type_borrow_to" value="borrow to" checked/>
                <label for="type_borrow_to"><?_e('borrow to', 'mytory-borrowing-cashbook')?></label>

                <input type="radio" name="borrowing_type" id="type_return_from" value="return from"/>
                <label for="type_return_from"><?_e('return from', 'mytory-borrowing-cashbook')?></label>

                <br>

                <input type="radio" name="borrowing_type" id="type_borrow_from" value="borrow from"/>
                <label for="type_borrow_from"><?_e('borrow from', 'mytory-borrowing-cashbook')?></label>

                <input type="radio" name="borrowing_type" id="type_return_to" value="return to"/>
                <label for="type_return_to"><?_e('return to', 'mytory-borrowing-cashbook')?></label>
            </td>
        </tr>
        <tr>
            <th>
                <label for="cash"><?_e('Cash', 'mytory-borrowing-cashbook')?></label>
            </th>
            <td>
                <input required="" type="tel" name="cash" id="cash"/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="date"><?_e('Date', 'mytory-borrowing-cashbook')?></label>
            </th>
            <td>
                <input required="" type="date" name="date" id="date" value="<?=date('Y-m-d')?>"/>
            </td>
        </tr>
        <tr>
            <th>
                <label for="person"><?_e('Person', 'mytory-borrowing-cashbook')?></label>
            </th>
            <td>
                <input required="" type="text" name="person" id="person"/>
                <span><?_e('This program don`t distinguish person with the same name', 'mytory-borrowing-cashbook')?></span>
            </td>
        </tr>
        <tr>
            <th>
                <label for="desc"><?_e('Description', 'mytory-borrowing-cashbook')?></label>
            </th>
            <td>
                <input type="text" name="desc" id="desc"/>
            </td>
        </tr>
    </table>
    <div class="mbc-button-area">
        <input type="submit" value="<? _e('Save') ?>"/>
    </div>
</form>

<div class="mbc-borrowing-list-container"></div>