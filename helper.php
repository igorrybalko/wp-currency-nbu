<?php

class CurrencyNbuHelper{
	
	private $cacheFile = __DIR__ . '/data.json';
 
	private function _writeCache()
	{
	    
	    file_put_contents($this->cacheFile, json_encode($this->_getNBURate()));
	 
	}
	 
	
	public function getRates($cache_time){

		$curTime = time(); 

		if (!file_exists($this->cacheFile)) {
		    $this->_writeCache($this->cacheFile);
		} else {
		    $fMtime = filemtime($this->cacheFile);
		    if (($curTime - $fMtime) > $cache_time) {
		        $this->_writeCache($this->cacheFile);
		    }
		}


		$rates = json_decode(file_get_contents($this->cacheFile), 1);

		return $rates;
	}

	private function _roundRate($rate){
	    $result = sprintf("%.2f", ceil( (float) $rate * 100) / 100);
	     return $result;
	}

	private function _getNBURate(){
		$date  = date('d.m.Y');
		$rates = [];
		$currency = json_decode(file_get_contents('http://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json'));

		if (is_array($currency)) {
			foreach($currency as $v){
				switch ($v->cc){
					case 'USD':
						$rateUSD = $this->_roundRate($v->rate);
						break;
					case 'EUR':
						$rateEUR = $this->_roundRate($v->rate);
						break;
					case 'RUB':
						$rateRUB = $this->_roundRate($v->rate);
						break;
				}
			}

			$rates = [
				'usd'   => $rateUSD,
				'eur'   => $rateEUR,
				'rub'   => $rateRUB,
				'date'  => $date
			];

		}

		return $rates;
	}
}