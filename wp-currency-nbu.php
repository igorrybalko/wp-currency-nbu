<?php
/*
Plugin Name: Currency NBU
Plugin URI: https://webstep.top
Description: Shows exchange rates USD, EUR and PLN
Version: 1.1.0
Author: Ihor Rybalko
Author URI: https://stringutils.online
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
*/

class WcnPluginConfig {
    public static $version = '1.1.0'; 
}

class WpCurrencyNbu extends WP_Widget
{
    public function __construct() {
        parent::__construct("wp_currency_nbu", "Currency NBU",
            ["description" => "Shows exchange rates USD, EUR and PLN"]);
    }

    public function form($instance) {

        $title = "";
        $cacheTime = 240;
        $tpl = 'default';

        foreach($instance as $k => $v){
            if($v){
                ${$k} = $v;
            }
        }

        $usd = isset( $instance['usd'] ) ? (int) $instance['usd'] : 1;
        $eur = isset( $instance['eur'] ) ? (int) $instance['eur'] : 1;
        $pln = isset( $instance['pln'] ) ? (int) $instance['pln'] : 1;

        $showDate = isset( $instance['showDate'] ) ? (int) $instance['showDate'] : 1;
        $style = isset( $instance['style'] ) ? (int) $instance['style'] : 1;

        $titleFieldId = $this->get_field_id("title");
        $titleFieldName = $this->get_field_name("title");
        echo '<p><label for="' . $titleFieldId . '">Title:</label><br>';
        echo '<input id="' . $titleFieldId . '" type="text" name="'
            . $titleFieldName . '" value="' . $title . '"></p>'; 
            
       
        $field_id_usd = $this->get_field_id( 'usd' );
        $field_name_usd = $this->get_field_name( 'usd' );
        ?>

        <p>
            <input 
                type="checkbox"
                id="<?php echo esc_attr( $field_id_usd ); ?>"
                name="<?php echo esc_attr( $field_name_usd ); ?>"
                value="1"
                <?php checked( $usd, 1 ); ?>
            />
            <label for="<?php echo esc_attr( $field_id_usd ); ?>">
                USD
            </label>
        </p>

        <?php

        $field_id_eur = $this->get_field_id( 'eur' );
        $field_name_eur = $this->get_field_name( 'eur' );
        ?>

        <p>
            <input 
                type="checkbox"
                id="<?php echo esc_attr( $field_id_eur ); ?>"
                name="<?php echo esc_attr( $field_name_eur ); ?>"
                value="1"
                <?php checked( $eur, 1 ); ?>
            />
            <label for="<?php echo esc_attr( $field_id_eur ); ?>">
                EUR
            </label>
        </p>

        <?php

        $field_id_pln = $this->get_field_id( 'pln' );
        $field_name_pln = $this->get_field_name( 'pln' );
        ?>

        <p>
            <input 
                type="checkbox"
                id="<?php echo esc_attr( $field_id_pln ); ?>"
                name="<?php echo esc_attr( $field_name_pln ); ?>"
                value="1"
                <?php checked( $pln, 1 ); ?>
            />
            <label for="<?php echo esc_attr( $field_id_pln ); ?>">
                PLN
            </label>
        </p>

        <?php

        $field_id_sdate = $this->get_field_id( 'showDate' );
        $field_name_sdate = $this->get_field_name( 'showDate' );
        ?>

        <p>
            <input 
                type="checkbox"
                id="<?php echo esc_attr( $field_id_sdate ); ?>"
                name="<?php echo esc_attr( $field_name_sdate ); ?>"
                value="1"
                <?php checked( $showDate, 1 ); ?>
            />
            <label for="<?php echo esc_attr( $field_id_sdate ); ?>">
                Show date
            </label>
        </p>

        <?php

        $field_id_style = $this->get_field_id( 'style' );
        $field_name_style = $this->get_field_name( 'style' );
        ?>

        <p>
            <input 
                type="checkbox"
                id="<?php echo esc_attr( $field_id_style ); ?>"
                name="<?php echo esc_attr( $field_name_style ); ?>"
                value="1"
                <?php checked( $style, 1 ); ?>
            />
            <label for="<?php echo esc_attr( $field_id_style ); ?>">
                Use plugin styles
            </label>
        </p>

        <?php
        $cacheTimeFieldId = $this->get_field_id("cacheTime");
        $cacheTimeFieldName = $this->get_field_name("cacheTime");
        echo '<p><label for="' . $cacheTimeFieldId . '">Cache time (in seconds):</label><br>';
        echo '<input id="' . $cacheTimeFieldId . '" type="number" name="' .
            $cacheTimeFieldName . '" value="' . $cacheTime . '"></p>';

        $listFiles = scandir(__DIR__ . '/tpl');
        $listTpls = array_map(function($el){
            preg_match('/^tpl_(.*)\.php$/', $el, $matches);
            if(isset($matches[1]))
                return $matches[1];
        }, $listFiles);
        $listTpls = array_filter($listTpls, function($el){
            return $el;
        });

        $tplFieldId = $this->get_field_id("tpl");
        $tplFieldName = $this->get_field_name("tpl");
        echo '<p><label for="' . $tplFieldId . '">Template:</label><br>';
        echo '<select name="' . $tplFieldName . '">';
        foreach($listTpls as $tplName){ ?>
            <option value="<?php echo $tplName ?>" <?php selected( $tpl, $tplName )?>><?php echo $tplName ?></option>
        <?php }

        echo '</select></p>';
    }

    public function update($newInstance, $oldInstance) {
        $values = [];
        $values["title"] = htmlentities($newInstance["title"]);
        $values["cacheTime"] = abs(intval($newInstance["cacheTime"]));
        $values["tpl"] = htmlentities($newInstance["tpl"]);

        $values["usd"] = isset( $newInstance["usd"] ) ? 1 : 0;
        $values["eur"] = isset( $newInstance["eur"] ) ? 1 : 0;
        $values["pln"] = isset( $newInstance["pln"] ) ? 1 : 0;

        $values["showDate"] = isset( $newInstance["showDate"] ) ? 1 : 0;
        $values["style"] = isset( $newInstance["style"] ) ? 1 : 0;

        return $values;
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        $title = $instance["title"];
        $cacheTime = $instance["cacheTime"];
        $tpl = isset($instance["tpl"]) ? $instance["tpl"] : 'default';

        $usd = isset( $instance['usd'] ) ? (int) $instance['usd'] : 1;
        $eur = isset( $instance['eur'] ) ? (int) $instance['eur'] : 1;
        $pln = isset( $instance['pln'] ) ? (int) $instance['pln'] : 1;

        $showDate = isset( $instance['showDate'] ) ? (int) $instance['showDate'] : 1;
        $style = isset( $instance['style'] ) ? (int) $instance['style'] : 1;

        if($style) {

            wp_enqueue_style(
                'widget_currency_nbu-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                [],
                WcnPluginConfig::$version
            );
        }

        require_once( __DIR__ . '/helper.php');

        $rates = new CurrencyNbuHelper;
        $data = $rates->getRates($cacheTime);

        require( __DIR__ . '/tpl/tpl_' . $tpl . '.php');

        echo $args['after_widget'];
    }

}
add_action("widgets_init", function () {
    register_widget("WpCurrencyNbu");
});