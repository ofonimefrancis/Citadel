<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 5/4/17
 * Time: 3:07 PM
 */

    namespace Citadel\Utility\Model\Storage\UrlRewrite;

    use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

    class DbStorage extends \Magento\UrlRewrite\Model\Storage\DbStorage {

        public function doReplace($urls){
            foreach ($this->createFilterDataBasedOnUrls($urls) as $type => $urlData) {
                $urlData[UrlRewrite::ENTITY_TYPE] = $type;
                $this->deleteByData($urlData);
            }
            $data = [];
            foreach ($urls as $url) {
                $data[] = $url->toArray();
            }

            /* FIXME: Get rid of rewrite for root Magento category to unduplicate things
             * @see: https://github.com/magento/magento2/issues/6671 */
            foreach($data as $key =>$info){
                if(isset($info['target_path']) && stristr($info['target_path'],'/category/1') && $info['entity_type']=='product'){
                    unset($data[$key]);
                }
            }

            $this->insertMultiple($data);
        }
    }
