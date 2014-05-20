<?php

namespace app\widgets\NavBarLangList;

use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;

/*
 * This is widget return dropdownList languges for nav menu
 */

class NavBarLangList extends \yii\bootstrap\Nav
{

    /**
     * Show or hide lang in menu
     * @var boolean
     */
    public $langList = false;

    /**
     * Method build list of languges
     * @return array
     */
    protected function langugesList()
    {
        // create list items
        foreach (Yii::$app->params['listLanguages'] as $suffix => $name) {
                $items[] = ['label'=> $name, 'url' => ['#'],
                    'active' => (($suffix == Yii::$app->language) ? TRUE: FALSE),
                    'linkOptions' => [
                        'data-lang' => $suffix,
                        'class' => 'langLink',
                        'onclick' => 'return false;'
                        ]];
        }

        return ['label' => 'Lang ['.Yii::$app->language .']', 'url' => ['#'], 'items' => $items];

    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $items = [];

        if ($this->langList) {
            $this->items = ArrayHelper::merge($this->items, [$this->langugesList()]);
            $this->registerClientScript();
        }

        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    /**
    * Register AssetBundle widget.
    */
   public function registerClientScript()
   {
           $view = $this->getView();

                   BootstrapPluginAsset::register($view);

                   $view->registerJs("jQuery(document).on('click', '.langLink', function(evt) {
                    lang = jQuery(this).data('lang');
                    jQuery.post('/',{_lang:lang}, function(data) {
                        if (data) {
                            location.reload();
                        }
                    });

});");

   }



}

