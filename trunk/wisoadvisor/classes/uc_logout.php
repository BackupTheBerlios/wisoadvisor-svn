<?php
//führt den Logout durch

class ucLogout extends UseCase
{
	//Ausführung: Business-Logik
	public function execute()
	{
		//wenn die Session zerstört werden kann ist alles ok,
		//dann auf den usecaseAfterLogout umleiten,
		//ansonsten Fehler werfen:
		if ($this->getSess()->destroy())
		{
			header('location:'.$this->getUsecaseLink($this->getConf()->getConfString('usecaseAfterLogout')));
			return true;
		}
		else
		{
			$this->setError('Die Session kann nicht beendet werden - Logout nicht möglich!');
			return false;
		}
	}

}
?>