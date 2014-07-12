<?php

class MysqlDbBuilder extends DbBuilder
{
    public function execute($aInputData)
    {
        $sSql = '';
		switch ($aInputData['Type']) {
			case 'SELECT':
				$sSql = 'SELECT ';
				if ($aInputData['SelectFields'][0]['Name'] == DbFetcher::SELECT_ALL && !$aInputData['SelectFields'][0]['Function']) {
					$sSql .= DbFetcher::SELECT_ALL;
				} else {
					foreach ($aInputData['SelectFields'] as $aSlF) {
						if ($aSlF['Alias']) {
                            if ($aSlF['Function']) {

                                if ($aSlF['FunctionParams']) {
                                    if (is_array($aSlF['FunctionParams'])) {
                                        $sSql.= $aSlF['Function'].'('.  implode(",", $aSlF['FunctionParams']).') as '.$aSlF['Alias'].',';
                                    } else {
                                        $sSql.= $aSlF['Function'].'('.$aSlF['FunctionParams'].') as '.$aSlF['Alias'].',';
                                    }
                                } else {
                                    $sSql.= $aSlF['Function'].'('.$aSlF['Name'].') as '.$aSlF['Alias'].',';
                                }
                            } else {
                                $sSql.= $aSlF['Name'].' as '.$aSlF['Alias'].',';
                            }
						} else {
							$sSql.= $aSlF['Name'].',';
						}
					}
					$sSql = substr($sSql, 0, strlen($sSql) - 1);
				}

				$sSql.= ' FROM `'.$this->addPrefix($aInputData['MainTable']['Name']).'` '.$aInputData['MainTable']['Alias'];

				if (isset($aInputData['Join'])) {
					foreach($aInputData['Join'] as $jntbl){
						$sSql.= ' '.$jntbl['Type'].' `'.$this->addPrefix($jntbl['Name']).'` '.$jntbl['Alias'].' ON '.$jntbl['Condition'];
					}
				}

				if (isset($aInputData['Criteria'])) {
					$sSql.= ' WHERE ' . $aInputData['Criteria'];
				}
                
                if (isset($aInputData['Group'])) {
					$sSql.=' GROUP BY ';
                    
					foreach ($aInputData['Group'] as $itm) {
						$sSql.= $itm.', ';
					}
					$sSql = substr($sSql, 0, strlen($sSql)-2);
				}

				if (isset($aInputData['Order'])) {
					$sSql.=' ORDER BY ';
					foreach ($aInputData['Order'] as $itm) {
						$sSql.= $itm['Field']." ". $itm['Direction'].', ';
					}
					$sSql = substr($sSql, 0, strlen($sSql)-2);
				}
                
				if (isset($aInputData['Limit'])) {
					if (isset($aInputData['Offset'])) {
						$sSql.= ' LIMIT '.$aInputData['Offset'].','.$aInputData['Limit'];
					} else {
						$sSql.= ' LIMIT '.$aInputData['Limit'];
					}
				}
			break;

			case 'INSERT':
				$sSql = "INSERT INTO `".$this->addPrefix($aInputData['MainTable']['Name'])."` SET ";
				foreach ($aInputData['Element'] as $key=>$val) {
					$sSql .= "`".$key."` = ".$this->toValidVar($val).", ";
				}
				$sSql = substr($sSql, 0, strlen($sSql)-2);
			break;

			case 'UPDATE':
				$sSql = "UPDATE `".$this->addPrefix($aInputData['MainTable']['Name'])."` SET ";
				foreach ($aInputData['Element'] as $key=>$val) {
                    /*if (is_object($val))
                    {
                        echo "`".$key."` = ".Core::p($val).", ";
                    }*/
                    
					$sSql .= "`".$key."` = ".$this->toValidVar($val).", ";
				}
				$sSql = substr($sSql, 0, strlen($sSql)-2);
				if (isset($aInputData['Criteria'])) {
					$sSql.= ' WHERE ' . $aInputData['Criteria'];
                }
			break;

			case 'DELETE':
				$sSql = "DELETE FROM `".$this->addPrefix($aInputData['MainTable']['Name'])."` ";
				if (isset($aInputData['Criteria'])) {
					$sSql.= ' WHERE ' . $aInputData['Criteria'];
				}
			break;
		}

		return $sSql;
    }
}