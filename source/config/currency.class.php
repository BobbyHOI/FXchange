<?php
/*
currencyLayer class - live currency conversion rates
version 1.0 12/11/2015

API reference at https://currencylayer.com/documentation

Copyright (c) 2015, Wagon Trader

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
class currencyLayer{
    
    //*********************************************************
    // Settings
    //*********************************************************
    
    //Your currencylayer API key
    //Available at https://currencylayer.com/product
    protected $apiKey = 'ba6afe4df350b5d489854122fd90d91d';
    
    //API endpoints
    private $endPoint = array(
        'live' => 'http://apilayer.net/api/live',
        'historical' => 'http://apilayer.net/api/historical',
        'convert' => 'http://apilayer.net/api/convert',
        'timeframe' => 'http://apilayer.net/api/timeframe',
        'change' => 'http://apilayer.net/api/change',
    );
    
    //current endpoint to use
    public $useEndPoint = 'live';
    
    //API key/value pair params
    public $params = array();
    
    //holds the error code, if any
    public $errorCode;
    
    //holds the error text, if any
    public $errorText;
    
    //response object
    public $response;
    
    //JSON response from API
    public $responseAPI;
    
    /*
    method:  convertCurrency
    usage:   convertCurrency(mixed amount, string to [,string from='USD']);
    params:  amount = from currency amount to convert 
             to = convert to currency
             from = from currency, defaults to USD
    
    This method will convert the supplied amount from specified currency to specified currency.
    
    returns: conversion result
    */
    public function convertCurrency($amount,$to,$from='USD'){
        
        $fromto = $from.$to;
        
        if( empty($this->response->quotes->$fromto) ){
            
            $this->resetParams();
            
            $this->setEndPoint('live');
            
            if( $from != 'USD' ){
                
                $this->setParam('source',$from);
                
            }
            
            $this->setParam('currencies',$to);
            
            $this->getResponse();
            
        }
        
        $result = $amount * $this->response->quotes->$fromto;
        
        return $result;
        
    }
    
    /*
    method:  getResponse
    usage:   getResponse(void);
    params:  none
    
    This method will build the reqeust and get the response from the API
    
    returns: null
    */
    public function getResponse(){
        
        $request = $this->buildRequest();
        
        $this->responseAPI = file_get_contents($request);
        
        $this->response = json_decode($this->responseAPI);
        
        if( !empty($this->response->error->code) ){
            
            $this->errorCode = $this->response->error->code;
            $this->errorText = $this->response->error->info;
            
        }
        
    }
    
    /*
    method:  buildRequest
    usage:   buildRequest([string useEndPoint=''])
    params:  useEndPoint = end point to use for this request
    
    This method will build the api request url.
    
    returns: api request url
    */
    public function buildRequest($useEndPoint=''){
        
        $useEndPoint = ( empty($useEndPoint) ) ? $this->useEndPoint : $useEndPoint;
        
        $request = $this->endPoint[$useEndPoint].'?access_key='.$this->apiKey;
        
        foreach( $this->params as $key=>$value ){
            
            $request .= '&'.$key.'='.$value;
            
        }
        
        return $request;
        
    }
    
    /*
    method:  setParam
    usage:   setParam(string key, string value);
    params:  key = key of the params key/value pair
             value =  value of the params key/value pair
    
    add or change the params key/value pair specified.
    
    returns: null
    */
    public function setParam($key,$value){
        
        $this->params[$key] = $value;
        
    }
    
    /*
    method:  resetParam
    usage:   resetParam(void);
    params:  none
    
    resets all stored parameters.
    
    returns: null
    */
    public function resetParams(){
        
        $this->params = array();
        
    }
    
    /*
    method:  setEndPoint
    usage:   setEndPoint(string useEndPoint);
    params:  useEndPoint = end point to use for request
    
    Sets the end point to use for request.
    
    returns: null
    */
    public function setEndPoint($useEndPoint){
        
        if( array_key_exists($useEndPoint,$this->endPoint) ){
            
            $this->useEndPoint = $useEndPoint;
            
        }else{
            
            throw new Exception($useEndPoint.' is not a valid end point');
            
        }
        
    }
    
}
?>
