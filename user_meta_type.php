<?php
class User_meta_type
{
    public static function text($user,$meta)
    {
        echo self::wrap($meta,'<input type="text" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function bool($user,$meta)
    {
        echo self::wrap($meta,'<label><input type="radio" class="regular-text ltr" id="'.$meta['slug'].'_0" name="'.$meta['slug'].'" value="0"> oui</label> <label><input type="radio" class="regular-text ltr" id="'.$meta['slug'].'_1" name="'.$meta['slug'].'" value="1"> non</label>');
    }

    public static function number($user,$meta)
    {
        echo self::wrap($meta,'<input type="number" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function email($user,$meta)
    {
        echo self::wrap($meta,'<input type="email" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function url($user,$meta)
    {
        echo self::wrap($meta,'<input type="url" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function color($user,$meta)
    {
        echo self::wrap($meta,'<input type="color" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function date($user,$meta)
    {
        echo self::wrap($meta,'<input type="date" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function datetime_local($user,$meta)
    {
        echo self::wrap($meta,'<input type="datetime-local" class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" value="'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'">');
    }

    public static function textarea($user,$meta)
    {
        echo self::wrap($meta,'<textarea class="regular-text ltr" id="'.$meta['slug'].'" name="'.$meta['slug'].'" >'.esc_attr(get_user_meta($user->ID, $meta['slug'], true)).'</textarea>');
    }

    public static function editor($user,$meta){
        ob_start();
        wp_editor(get_user_meta($user->ID, $meta['slug'], true), $meta['slug'], $settings = array('textarea_name' => $meta['slug']));
        echo self::wrap($meta,ob_get_clean());
    }
    

    private static function wrap($meta,$content){
        return '<table class="form-table">
        <tr>
            <th>
                <label for="'.$meta['slug'].'">'.$meta['name'].'</label>
            </th>
            <td>
                '.$content.'
                <p class="description">
                '.$meta['description'].'
                </p>
            </td>
        </tr>
    </table>';
    }
}
