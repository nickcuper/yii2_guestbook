<?php
namespace app\extensions\langurlmanager;

use Yii;
use yii\web\UrlManager;


class LangUrlManager extends UrlManager {


    public function createUrl($params)
    {
        $url = parent::createUrl($params);
        return $this->addLangToUrl($url);
    }

    /**
     * This method add prefix lang to url
     * @param string $url
     * @return string
     */
    public static function addLangToUrl($url)
    {
            if (self::enabled())
            {
                    $domains = explode('/', ltrim($url, '/'));

                    $isHasLang = in_array($domains[0], array_keys(Yii::$app->params['listLanguages']));
                    $isDefaultLang = (Yii::$app->language == Yii::$app->params['defaultLanguges']);

                    if ($isHasLang && $isDefaultLang)
                        array_shift($domains);

                    // if not found
                    if (!$isHasLang && !$isDefaultLang)
                        array_unshift($domains, Yii::$app->language);

                    $url = '/' . implode('/', $domains);

            }

            return $url;
    }

    /**
     * Checked has list
     * @return boolean
     */
    public static function enabled()
    {
        return (bool)(sizeof(Yii::$app->params['listLanguages']) > 1);
    }

}
