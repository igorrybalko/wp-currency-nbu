<?php
/*
Plugin Name: Currency NBU
Plugin URI: https://webstep.top
Description: Shows exchange rates USD, EUR and PLN
Version: 1.1.0
Author: Rybalko Ihor
Author URI: https://stringutils.online
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
*/

class WpCurrencyNbu extends WP_Widget
{
    public function __construct() {
        parent::__construct("widget_currency_nbu", "Currency NBU",
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

        $titleFieldId = $this->get_field_id("title");
        $titleFieldName = $this->get_field_name("title");
        echo '<p><label for="' . $titleFieldId . '">Title:</label><br>';
        echo '<input id="' . $titleFieldId . '" type="text" name="'
            . $titleFieldName . '" value="' . $title . '"></p>';


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
        $values = array();
        $values["title"] = htmlentities($newInstance["title"]);
        $values["cacheTime"] = abs(intval($newInstance["cacheTime"]));
        $values["tpl"] = htmlentities($newInstance["tpl"]);
        return $values;
    }

    public function widget($args, $instance) {
        $title = $instance["title"];
        $cacheTime = $instance["cacheTime"];
        $tpl = $instance["tpl"];

        require_once( __DIR__ . '/helper.php');

        $rates = new CurrencyNbuHelper;
        $data = $rates->getRates($cacheTime);

        require_once( __DIR__ . '/tpl/tpl_' . $tpl . '.php');
    }

}
add_action("widgets_init", function () {
    register_widget("WpCurrencyNbu");
});