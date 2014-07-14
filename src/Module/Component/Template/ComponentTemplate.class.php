<?php

/**
 * ComponentTemplate: component templates system implementation
 *
 * @package Module
 * @subpackage Component
 * @version 7.1
 */
class ComponentTemplate extends Ruxon
{
    /**
     * Данные
     *
     * @var array
     */
    protected $aData = array();

    protected $sModuleAlias;

    protected $sComponentAlias;

    protected $sTemplate;


    public function __construct($sModule, $sComponent, $sTemplate = 'Index', $aParams = array())
    {
        $this->sModuleAlias = $sModule;
        $this->sComponentAlias = $sComponent;
        $this->sTemplate = $sTemplate;
        $this->aData = $aParams;
    }

    public function fetch()
    {
        $sTemplateFinal = ucfirst($this->sTemplate);

        $sBaseTemplate = Core::app()->theme()->getName();

        $module = Core::app()->getModuleById($this->sModuleAlias);
        $infoPath = empty($module['BasePath']) ? RX_PATH.'/ruxon/modules/'.$this->sModuleAlias.'/components/'.$this->sComponentAlias : RX_PATH.'/'.$module['BasePath'].'/components/'.$this->sComponentAlias;


        $sTemplateTemplateFile = RX_PATH.'/themes/'.$sBaseTemplate.'/components/'.$this->sModuleAlias.'/'.$this->sComponentAlias.'/'.$sTemplateFinal.'.tpl.php';
        $sTemplateComponentFile = $infoPath.'/templates/'.$sBaseTemplate.'/'.$sTemplateFinal.'.tpl.php';
        $sTemplateDefaultComponentFile = $infoPath.'/templates/default/'.$sTemplateFinal.'.tpl.php';

        ob_start();

        if (file_exists($sTemplateTemplateFile)) {
            include($sTemplateTemplateFile);
        } else if (file_exists($sTemplateComponentFile)) {
            include($sTemplateComponentFile);
        } else if (file_exists($sTemplateDefaultComponentFile)) {
            include($sTemplateDefaultComponentFile);
        } else {
            throw new RxException('Не найден шаблон "'.$sTemplateFinal.'" для компонента "'.$this->sComponentAlias.'".');
        }

        $sResult = "<!-- Component: '".$this->sModuleAlias.".".$this->sComponentAlias."' -->\r\n";
        $sResult .= ob_get_contents()."\r\n";
        $sResult .= "<!-- End of Component: '".$this->sModuleAlias.".".$this->sComponentAlias."' -->\r\n";
        ob_end_clean();

        return $sResult;
    }

    public function theme()
    {
        return Core::app()->theme();
    }

    public function module_config($alias)
    {
        return Manager::getInstance()->getModule($this->sModuleAlias)->config($alias);
    }

    public function component($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        return $this->theme()->layout()->component($sModuleAlias, $sComponentAlias, $aParams);
    }

    public function widget($sModuleAlias, $sComponentAlias, $aParams = array())
    {
        echo $this->theme()->layout()->widget($sModuleAlias, $sComponentAlias, $aParams);
    }

    public function t($category, $message, $params = [], $language = null)
    {
        return Core::app()->t($category, $message, $params, $language, 'ruxon/modules/'.$this->sModuleAlias.'/components/'.$this->sComponentAlias.'/messages');
    }
}