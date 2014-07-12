<?php

/**
 * FormHelper
 *
 * @package Core
 * @subpackage Helpers
 * @version 5.2.1
 */
class FormHelper
{

	public static function label($sTitle, $bIsRequired = false)
	{
		return '<label>'.$sTitle.'</label>'.($bIsRequired ? '<span class="required">*</span>' : '');
	}

	public static function labelFor($sToFieldId, $sTitle, $bIsRequired = false)
	{
		return '<label for="'.$sToFieldId.'">'.$sTitle.'</label>'.($bIsRequired ? '<span class="required">*</span>' : '');
	}

	public static function textbox($sFieldName, $sValue, $aParams = array())
	{
		$sResult = '<input type="text" '.self::parseParamsString($aParams).' name="'.$sFieldName.'" value="'.$sValue.'" />';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}
    
    public static function hidden($sFieldName, $sValue, $aParams = array())
	{
		$sResult = '<input type="hidden" '.(isset($aParams['disabled']) && $aParams['disabled'] ? 'disabled="disabled"' : '').' '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' name="'.$sFieldName.'" '.(isset($aParams['id']) ? 'id="'.$aParams['id'].'"' : '').' value="'.$sValue.'" />';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}
    
    public static function file($sFieldName, $sValue, $aParams = array())
	{
		$sResult = '<input type="file" '.(isset($aParams['disabled']) && $aParams['disabled'] ? 'disabled="disabled"' : '').' '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' name="'.$sFieldName.'" '.(isset($aParams['id']) ? 'id="'.$aParams['id'].'"' : '').' value="'.$sValue.'" />';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}

	public static function password($sFieldName, $sValue = '', $aParams = array())
	{
		return '<input type="password" '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' name="'.$sFieldName.'" '.(isset($aParams['id']) ? 'id="'.$aParams['id'].'"' : '').' value="" />';
	}

	public static function textarea($sFieldName, $sValue, $aParams = array())
	{
		$sResult = '<textarea name="'.$sFieldName.'" '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' rows="" cols="" '.(isset($aParams['id']) ? 'id="'.$aParams['id'].'"' : '').'>'.$sValue.'</textarea>';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}

	public static function selectbox($sFieldName, $mValue, $aValues, $aParams = array())
	{
		$sResult = '<select '.self::parseParamsString($aParams).' name="'.$sFieldName.'">';

		foreach ($aValues as $val) {
			$sResult .= '<option '.($mValue == $val['Value'] ? 'selected="selected"' : '').' value="'.$val['Value'].'">'.$val['Name'].'</option>';
		}

		$sResult .= '</select>';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}
    
    public static function multiselect($sFieldName, $mValue, $aValues, $aParams = array())
	{
		$sResult = '<select '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' '.(isset($aParams['id']) ? 'id="'.$aParams['id'].'"' : '').' name="'.$sFieldName.'" multiple="multiple">';

		foreach ($aValues as $val) {
			$sResult .= '<option '.(in_array($val['Value'], (array) $mValue) !== false  ? 'selected="selected"' : '').' value="'.$val['Value'].'">'.$val['Name'].'</option>';
		}

		$sResult .= '</select>';

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}
    
    public static function checkboxGroup($sFieldName, $mValue, $aValues, $aParams = array())
	{
        $sResult = '';
        
		foreach ($aValues as $val) {
			$sResult .= '<label><input type="checkbox" name="'.$sFieldName.'" '.(in_array($val['Value'], (array) $mValue) !== false  ? 'checked="checked"' : '').' value="'.$val['Value'].'" /> '.$val['Name'].'</label><br />';
		}

		if (isset($aParams['help'])) {
			$sResult .= '<p>'.$aParams['help'].'</p>';
		}

		return $sResult;
	}
    
    public static function checkbox($sFieldName, $bChecked = false, $aParams = array())
	{
		$sResult = '<input type="hidden" name="'.$sFieldName.'" value="0" />';

        $sResult .= '<input name="'.$sFieldName.'" type="checkbox" value="1" '.($bChecked ? 'checked="checked"' : '').' '.self::parseParamsString($aParams).'  />';

		return $sResult;
	}

	public static function radio($sFieldName, $mValue, $aValues, $aParams = array())
	{
		$sResult = '';

		foreach ($aValues as $val) {
			$sResult .= '<div><input name="'.$sFieldName.'" type="radio" value="'.$val['Value'].'" '.($mValue == $val['Value'] ? 'checked="checked"' : '').' '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' id="'.$sFieldName.$val['Value'].'"  /><label for="'.$sFieldName.$val['Value'].'">'.$val['Name'].'</label></div>';
		}

		return $sResult;
	}

	public static function boolean($sFieldName, $bValue, $aParams = array())
	{
		$aValues = array(
			array(
				'Name' => 'Да',
				'Value' => 1
			),

			array(
				'Name' => 'Нет',
				'Value' => 0
			)
		);
		$sResult = '';

		foreach ($aValues as $val) {
			$sResult .= '<div><input name="'.$sFieldName.'" type="radio" value="'.$val['Value'].'" '.($bValue == $val['Value'] ? 'checked="checked"' : '').' '.(isset($aParams['class']) ? 'class="'.$aParams['class'].'"' : '').' id="'.$sFieldName.$val['Value'].'"  /><label for="'.$sFieldName.$val['Value'].'">'.$val['Name'].'</label></div>';
		}

		return $sResult;
	}
	   
    public static function parseParamsString($aParams = array())
    {
        $params_str = '';
        if (count($aParams)) 
        {
            foreach ($aParams as $k => $par) {
                $params_str .= $k . '="'.$par.'" ';
            }
        }
        
        return $params_str;
    }


}