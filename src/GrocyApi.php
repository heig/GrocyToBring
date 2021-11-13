<?php

class GrocyApi {

    const GET_REQUEST = 'get';
    const POST_REQUEST = 'post';
    const PUT_REQUEST = 'put';




    public function __construct($grocyRestURL, $grocyApiKey)
    {
      
        $this->grocyRestURL = $grocyRestURL;
        $this->grocyApiKey = $grocyApiKey;

    }

    public function getVolatileProducts($due_days)
    {
      return json_decode($this->request(self::GET_REQUEST,"stock/volatile","?due_soon_days=".$due_days, true));
    }

    public function getProductEntity($productid)
    {
      return json_decode($this->request(self::GET_REQUEST,"objects/products/".$productid, '',true));
    }

    public function checkHideFromBring($productid, $hidefrombring)
    {
      $result = $this->getProductEntity($productid);
      if($result->userfields->$hidefrombring == 1){
        return true;
      }else{
        return false;
      }
    }

    public function getShoppingListItmes()
    {
      
       $list = json_decode($this->request(self::GET_REQUEST,"objects/shopping_list",'', true));
       foreach($list as $l){
         //$list_new[]['name'] = $this->getProductEntity($l->product_id)->name;
         //$list_new[]['id'] = $l->product_id;
         $list_new[] = ['name' => $this->getProductEntity($l->product_id)->name,
                        'id' => $l->product_id,
                        'amount_missing' => $l->amount 
                        ];  
       }
       return json_decode(json_encode($list_new));
    }

     /**
  *   Handles the request to the server
  *
  *   @param const string $type   The HTTP request type.
  *   @param string $request      contains the request URL
  *   @param string $parameter    The parameters we send with the request
  *   @param bool $customHeader   True if you want to send the custom header (That is necessary because it sends the API-KEY) with the request
  *   @return The answer string from the server
  */
  private function request($type = self::GET_REQUEST,$request, $parameter, $customHeader = false)
  {
    $ch = curl_init();
    $additionalHeaderInfo = "";
    switch($type) {
      case self::GET_REQUEST:
        curl_setopt($ch, CURLOPT_URL, $this->grocyRestURL.$request.$parameter);
      break;
      case self::POST_REQUEST:
        curl_setopt($ch, CURLOPT_URL, $this->grocyRestURL.$request);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$parameter);
      break;
      case self::PUT_REQUEST:
        $fh = tmpfile();
        fwrite($fh, $parameter);
        fseek($fh, 0);
        curl_setopt($ch, CURLOPT_URL, $this->grocyRestURL.$request);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($parameter));
        $additionalHeaderInfo = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
      break;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($customHeader) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHeader(($additionalHeaderInfo != "")?$additionalHeaderInfo:null));
    }
    $server_output = curl_exec ($ch);
    $this->answerHttpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);

    return $server_output;
  }
  /**
  *   @param string|array $additional   additional field that you want to add to the header
  *   @return array with the headerinformation
  */
  private function getHeader($additional = null)
  {
    $header = [
      "GROCY-API-KEY: $this->grocyApiKey",
    ];
    if($additional != null) {
      if(is_array($additional)) {
        foreach($additional as $key => $value) {
          $header[] = $value;
        }
      } else {
        $header[] = $additional;
      }
    }
    return $header;
  }

}