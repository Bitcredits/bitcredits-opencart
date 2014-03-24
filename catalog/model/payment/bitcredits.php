<?php 

class ModelPaymentBitcredits extends Model {
  	public function getMethod($address) {
		$this->load->language('payment/bitcredits');
		
		if ($this->config->get('bitcredits_status')) {
        	$status = TRUE;
		} else {
			$status = FALSE;
		}
		
		$method_data = array();
	
		if ($status) {  
      		$method_data = array( 
        		'code'         	=> 'bitcredits',
        		'title'      	=> $this->language->get('text_title'),
				'sort_order' 	=> $this->config->get('bitcredits_sort_order'),
      		);
    	}
   
    	return $method_data;
  	}
}
?>
