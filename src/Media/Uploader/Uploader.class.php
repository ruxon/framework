<?php

class Uploader
{
	protected $sFolderName = 'images';

	protected $aAllowExt = array('jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'pdf', 'ppt', 'xls', 'xlsx');

	public function Upload($sFieldName, $sFolder = '/images', $nIndex = false)
	{
		$sName = ($nIndex !== false ? $_FILES[$sFieldName]['name'][$nIndex] : $_FILES[$sFieldName]['name']);
		$sTmpName = ($nIndex !== false ? $_FILES[$sFieldName]['tmp_name'][$nIndex] : $_FILES[$sFieldName]['tmp_name']);
		$sSize = ($nIndex !== false ? $_FILES[$sFieldName]['size'][$nIndex] : $_FILES[$sFieldName]['size']);
		$sType = ($nIndex !== false ? $_FILES[$sFieldName]['type'][$nIndex] : $_FILES[$sFieldName]['type']);

		if (isset($sName) && $sTmpName){
			//$sNewFileName = md5(rand(1000, 10000).time());


			$sExtension = strtolower(substr($sName, strrpos($sName, ".") + 1));
			$sRealName = strtolower(substr($sName, 0, strlen($sName) - strlen($sExtension) - 1));

			if (array_search($sExtension, $this->aAllowExt) !== false) {
				//if (!is_dir(RX_UPLOADS_DIR.'/'.$sFolder.'/'.$sNewFileName)) {
					//@mkdir(RX_UPLOADS_DIR.'/'.$sFolder.'/'.$sNewFileName, 0777);
					$sFileFullPath = $sFolder.'/'.$sRealName.'.'.$sExtension;
					if (move_uploaded_file($sTmpName, RX_RUXON_UPLOADS_PATH.$sFileFullPath)) {

						//file_put_contents("d://1files_file_".$sFieldName."_".$nIndex.".txt", RX_RUXON_UPLOADS_PATH.$sFileFullPath);
						return $sFileFullPath;
					}
				//}
			} else {
				unlink($sTmpName);
			}
		}

		return false;
	}

	protected function getToolkit()
	{
		return Toolkit::getInstance();
	}
}