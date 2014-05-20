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
        // current url
        $currentUrl = ltrim(Yii::$app->request->url, '/');

        $enabled = (bool) (sizeof(Yii::$app->params['listLanguages']) > 1);
        $items = array();
        $listLang = array();

        // build list of languges
        foreach (Yii::$app->params['listLanguages'] as $lang => $name)
        {
                if ($lang === Yii::$app->params['defaultLanguges']) {
                        $suffix = '';
                        $listLang[$suffix] = $enabled ? $name : '';
                } else {
                        $suffix = '_' . $lang;
                        $listLang[$suffix] = $name;
                }
        }

        // create list items
        foreach ($listLang as $suffix => $name) {
                $url = '/' . ($suffix ? trim($suffix, '_') . '/' : '') . $currentUrl;
                $items[] = ['label'=> $name.'['.$url, 'url' => [$url], 'active' => ($suffix) ? false: true];
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

}

