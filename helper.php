<?php 
class CurrencyNbuHelper{
	
	private $cacheFile = __DIR__ . '/data.json';
 
	public function getRates($cache_time){

		if (file_exists($this->cacheFile) && (filemtime($this->cacheFile) > (time() - $cache_time))) {
			// Getting data from the cache
			$file = file_get_contents($this->cacheFile);

			return json_decode(file_get_contents($this->cacheFile), 1);
		} 
			
		$rates = $this->_getNBURate();
		file_put_contents($this->cacheFile, json_encode($rates), LOCK_EX);
	
		return $rates;
	}

	private function _roundRate($rate){
	    return sprintf("%.2f", ceil( (float) $rate * 100) / 100);
	}

	private function _getNBURate(){
		$date  = date('d.m.Y');
		$rates = [
			'usd'   => '',
			'eur'   => '',
			'pln'   => '',
			'date'  => $date,
		];

		$currency = json_decode(file_get_contents('https://bank.gov.ua/NBUStatService/v1/statdirectory/exchange?json'));

		if (is_array($currency)) {
			foreach($currency as $v){
				switch ($v->cc){
					case 'USD':
						$rateUSD = $this->_roundRate($v->rate);
						break;
					case 'EUR':
						$rateEUR = $this->_roundRate($v->rate);
						break;
					case 'PLN':
						$ratePLN = $this->_roundRate($v->rate);
						break;
				}
			}

			$rates = [
				'usd'   => $rateUSD,
				'eur'   => $rateEUR,
				'pln'   => $ratePLN,
				'date'  => $date,
			];

		}

		return $rates;
	}
}
