<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 27.02.13
 * Time: 09:35
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Merge_Core_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    const HANDLE_ATTRIBUTE = 'data-handle'; //attribute used to store handle

    protected function _checkMatch($item, $matches)
    {
        foreach (['file', 'name', 'script', 'stylesheet'] as $type) {
            if (isset($item->{$type})) {
                $filename = (string) $item->{$type};
                break;
            }
        }
	
	$matches = array_filter($matches);
        foreach ($matches as $match) {
            if (strpos($filename, $match) !== false) {
                return $filename;
            }
        }

        return false;
    }

    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        $xml = parent::getFileLayoutUpdatesXml($area, $package, $theme, $storeId);
        if (Mage::getDesign()->getArea() != 'adminhtml') {
            $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files') &&
                Mage::getStoreConfigFlag('dev/js/merge_js_by_handle');
            $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files') &&
                Mage::getStoreConfigFlag('dev/css/merge_css_by_handle');

            $excludeJs = array_map('trim', explode(',', Mage::getStoreConfig('dev/js/merge_js_excludes')));
            $excludeCss = array_map('trim', explode(',', Mage::getStoreConfig('dev/css/merge_css_excludes')));

            $methods = array();
            if ($shouldMergeJs) {
                $methods[] = 'addJs';
            }
            if ($shouldMergeCss) {
                $methods[] = 'addCss';
            }
            if ($shouldMergeJs || $shouldMergeCss) {
                $methods[] = 'addItem';
            }
            foreach ($methods as $method) {
                foreach ($xml->children() as $handle => $child) {
                    $items = $child->xpath(".//action[@method='".$method."']");
                    foreach ($items as $item) {
                        $paramsHandle = $handle;

                        if ((in_array((string)$item->{'type'}, ['skin_js', 'js']) OR $method == 'addJs') && $itemName = $this->_checkMatch($item, $excludeJs)) {
                                $paramsHandle = $itemName;
                        } else if ((string)$item->{'type'} == 'skin_css' && $itemName = $this->_checkMatch($item, $excludeCss)) {
                                $paramsHandle = $itemName;
                        }

                        if ($method == 'addItem' && ((!$shouldMergeCss && (string)$item->{'type'} == 'skin_css') || (!$shouldMergeJs && (string)$item->{'type'} == 'skin_js'))) {
                            continue;
                        }

                        $params = $item->xpath("params");
                        if (count($params)) {
                            foreach ($params as $param){
                                if (trim($param)) {
                                    $param[0] = (string)$param . ' ' . static::HANDLE_ATTRIBUTE . '="' . $paramsHandle . '"';
                                } else {
                                    $param[0] = static::HANDLE_ATTRIBUTE . '="' . $paramsHandle . '"';
                                }
                            }
                        } else {
                            $result = $item->addChild('params', static::HANDLE_ATTRIBUTE . '="'.$paramsHandle.'"');
                        }
                    }
                }
            }
        }
        return $xml;
    }

}

