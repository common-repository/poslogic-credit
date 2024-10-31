<?php
if($error_message){
    echo  "<div class='error'>".$error_message."</div>";
}else {
    ?>
    <table>
        <?php
        foreach ($wc_categories as $wc_key => $wc_category) {
            ?>
            <tr>
                <td width="150"><b><?php echo esc_attr($wc_category->name); ?></b></td>
                <td>
                    <select class="select2-selection__rendered"
                            name="categories[<?php echo (int)$wc_category->term_id; ?>]">
                        <option value="0">Нет</option>
                        <?php
                        foreach ($poslogic_categories as $poslogic_key => $poslogic_category) {
                            $selected = "";
                            if (
                                isset($wc_poslogic_categories[$wc_category->term_id])
                                && $wc_poslogic_categories[$wc_category->term_id] == $poslogic_category->id
                            ) {
                                $selected = " selected";
                            }
                            print "<option value='" . (int)$poslogic_category->id . "'" . $selected . ">" . esc_attr($poslogic_category->name) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <br/>
    <?php
}
?>
<a href="<?php echo esc_url($return_href)?>" class="add-new-h2"><?php echo __('Rerurn back', 'poslogic-credit')?></a>
