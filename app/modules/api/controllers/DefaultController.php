<?php
namespace app\modules\api\controllers;

use Yii;
use yii\helpers\Json;
use yii\web\Controller;

use app\modules\api\models\StatisticRead;
use app\modules\api\models\StatisticWrite;

class DefaultController extends Controller
{

        public $enableCsrfValidation = false;
        /**
         * Store prepare request
         * @var array
         */
        protected $request = array();

        public function init()
        {
                $this->setRequest();

                // TODO add Token veretifications
        }
        /**
         * Get id from response and find record in StatisticRead if record find write to StatisticWrite
         * @return json
         */
        public function actionRun()
        {
            $params = $this->getParam('id');

            if ($params) {

                $mStatisticRead = StatisticRead::findOne($params);

                if ($mStatisticRead) {

                     // init transations
                    $transactions = Yii::$app->db->beginTransaction();

                    $mStatisticWrite = new StatisticWrite();
                    $mStatisticWrite->random_var = md5($mStatisticRead->random_var . ' ' . $mStatisticRead->statistic_read_id);
                    $mStatisticWrite->date_created = date ('Y-m-d H:i:s');

                    if ($mStatisticWrite->save()) {
                            $transactions->commit();

                    } else {
                            $transactions->rollBack();
                    }

                }
            }
        }

        /**
         * Prepare request
         */
        protected function setRequest()
        {
                $request = file_get_contents('php://input');

                if ($request) {
                        $request_json = Json::decode($request);
                        if(is_array($request_json)) $this->request = $request_json;
                }
        }

        /**
         *
         * @return array
         */
        public function getRequest()
        {
                return $this->request;
        }

        /**
         *
         * @param string $name params name
         * @param null $default Default variable return if not found $name
         * @return array||null
         */
        public function getParam($name, $default=null)
        {
                if(isset($this->request[$name])) return $this->request[$name];

                return $default;
        }

}