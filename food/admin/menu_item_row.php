<div class="menu-row" style="margin-bottom:5px;">
    <select name="menu[<?= $day ?>][<?= $meal ?>][][item_id]" class="form-control input-sm"
        style="width:65%;display:inline-block">

        <option value="">Select Item</option>
        <?php
        $items = mysqli_query($con, "SELECT * FROM rrsv_menu_package WHERE status='Active'");
        while ($i = mysqli_fetch_assoc($items)) {
            $sel = (isset($m['item_id']) && $m['item_id'] == $i['id']) ? 'selected' : '';
            echo "<option value='{$i['id']}' $sel>{$i['item_name']}</option>";
        }
        ?>
    </select>

    <input type="number" step="0.01" name="menu[<?= $day ?>][<?= $meal ?>][][qty]" value="<?= $m['qty'] ?? 1 ?>"
        class="form-control input-sm" style="width:30%;display:inline-block">

</div>