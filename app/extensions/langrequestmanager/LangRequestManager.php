<?php
namespace app\extensions\langrequestmanager;

use Yii;
use yii\web\Request;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class LangRequestManager extends Request {

    private $_requestUri;

    public function getUrl()
    {
        if ($this->_requestUri === null)
            $this->_requestUri = $this->processLangInUrl(parent::getUrl());

        return $this->_requestUri;
    }

    public function getOriginalUrl()
    {
        return $this->getOriginalRequestUri();
    }

    public function getOriginalRequestUri()
    {
        return $this->addLangToUrl($this->getRequestUri());
    }

    public static function processLangInUrl($url)
    {
        if (self::enabled())
        {
            $domains = explode('/', ltrim($url, '/'));

            $isLangExists = in_array($domains[0], array_keys(Yii::$app->params['listLanguages']));
            $isDefaultLang = $domains[0] == Yii::$app->params['defaultLanguges'];

            if ($isLangExists && !$isDefaultLang)
            {
                    $lang = array_shift($domains);
                    Yii::$app->language = $lang;
            }

            $url = '/' . implode('/', $domains);
        }

        return $url;
    }

    /**
     *
     * @return boolean
     */
    public static function enabled()
    {
        return (sizeof(Yii::$app->params['listLanguages']) > 1);
    }

    public static function addLangToUrl($url)
    {
        if (self::enabled())
        {
            $domains = explode('/', ltrim($url, '/'));
            $isHasLang = in_array($domains[0], array_keys(Yii::$app->params['listLanguages']));
            $isDefaultLang = Yii::$app->language == Yii::$app->params['defaultLanguges'];

            if ($isHasLang && $isDefaultLang)
                array_shift($domains);

            if (!$isHasLang && !$isDefaultLang)
                array_unshift($domains, Yii::$app->language);

            $url = '/' . implode('/', $domains);
        }

        return $url;
    }
}
