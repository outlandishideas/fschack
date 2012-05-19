<?php  

class Model_Investigation extends Model_Base{	
	protected $_tableName = 'investigations';

	static function insert($data){
		$investigation = new Model_Investigation($data);
		$investigation->save();
		foreach($data->siteInvestigations as $siteInvestigation){
			$site = Model_Site::fetchByCentreAndTitle($investigation->centre, $siteInvestigation->name);
			if(!$site){
				$site = new Model_Site(array($investigation->centre, $siteInvestigation->name));
				$site->save();
			}
			$siteInv = new Model_Investigation($siteInvestigation);
			$siteInv->siteId = $site->id;
			$siteInv->investigationId = $investigation->id;
			$siteInv->save();

			foreach($siteInvestigation->data as $type => $values){
//				$q = "INSERT INTO measurements (siteInvestigationId, type, investigationSeriesIndex, value) VALUES (?,?,?,?)";
				foreach($values as $i => $value){
					$tmp = array(
						'siteInvestigationId' => $siteInvestigation->id,
						'type' => $type,
						'investigationSeriesIndex' => $i,
						'value' => $value,
					);
					$measurement = new Model_Measurement($tmp);
					$measurement->save();
				}
			}
		}

	}

	public function getSiteInvestigations() {
		$stmt = $this->_db->prepare('SELECT * FROM siteinvestigations WHERE investigationId = :id');
		$stmt->execute(array(':id'=>$this->id));
		return $this->siteInvestigations = $this->objectify($stmt);
	}

}
