<?php

/*
 * BDX Lead Posting
 */

namespace BDX;

class LeadPosting
{
    
    /*
	 * BDX Partner ID
	 * @var int
	 */
    private $partnerID;
    
    /*
	 * BDX Lead Posting Token
	 * @var string
	 */
    private $token;
    
    /*
	 * Error variable populated with a description of the error
	 * @var string
	 */
    private $error;
    
    /*
	 * Current User/Lead being posted
	 * @var object
	 */
    private $user;
    
    /*
	 * SOAP Server URL
	 * @var string
	 */
    
    private $soapURL = 'http://leads.newhomesource.com/LeadPostingService.svc?wsdl';
    
    /*
	 * SOAP Version
	 * @var string
	 */
    
    private $soapVersion = 'SOAP_1_1';
    
    /*
	 * List of possible paramters and their configurations(type, length)
	 * @var array
	 */
    private $parameterConfig = array(
        'Address' => array(
            'type'      => 'string',
            'length'    => '100',
        ),
        'BuilderId' => array(
            'type'      => 'int',
        ),
        'City' => array(
            'type'      => 'string',
            'length'    => '50',
        ),
        'CommentRemarks' => array(
            'type'      => 'string',
            'length'    => '1000',
        ),
        'CommunityId' => array(
            'type'      => 'int',
        ),
        'DayPhone' => array(
            'type'      => 'string',
            'length'    => '20',
        ),
        'DayPhoneExt' => array(
            'type'      => 'string',
            'length'    => '10',
        ),
        'EmailAddress' => array(
            'type'      => 'string',
            'length'    => '100',
        ),
        'FinancingPreference' => array(
            'type'      => 'string',
            'length'    => '40',
        ),
        'FirstName' => array(
            'type'      => 'string',
            'length'    => '50',
            'required'  => true
        ),
        'LastName' => array(
            'type'      => 'string',
            'length'    => '50',
            'required'  => true
        ),
        'ListingIds' => array(
            'type'      => 'array',
        ),
        'MoveInDays' => array(
            'type'      => 'string',
            'options'   => array(
                'Any',
                'Immediate',
                '1 Month',
                '3 months',
                '6 months'
            )
        ),
        'PostalCode' => array(
            'type'      => 'string',
            'length'    => '10',
        ),
        'State' => array(
            'type'      => 'string',
            'length'    => '2',
        ),
        'Title' => array(
            'type'      => 'string',
            'length'    => '3',
        ),
            
    );
    
    /*
	 * Constructor
	 * @param object $user (Assumes the user is valid and this object is correctly passed)
	 * @return void
	 */
    function __construct($user)
    {
        $this->partnerID = Settings::getInstance()->partnerID;
        $this->token = Settings::getInstance()->token;
        $this->user = $user;
    }
        
    /*
	 * Posts the lead to the SOAP server
	 * @param string $type 'listing' or 'community'
	 * @array $options An associative array of options. The provided key for each item must correspond to the parameter in the parameterConfig array
	 * Examples:
	 * 		<code>
	 *		$options = array(
	 *			'Address' => 'Example Address'
	 *			'BuilderId' => 123456
	 *			'City' => 'Example City'
	 *			'CommentRemarks' => 'Example Remarks'
	 *			'CommunityId' => 654321
	 *			'DayPhone' => '123456789'
	 *			'DayPhoneExt' => '123'
	 *			'EmailAddress' => 'test@realestatewebmasters.com'
	 *			'FinancingPreference' => 'Test Preference'
	 *			'FirstName' => 'FirstName'
	 *			'LastName' => 'LastName'
	 *			'ListingIds' => array('id1', 'id2', 'id3', 'id4')
	 *			'MoveInDays' => '1 Month'
	 *			'PostalCode' => '123456'
	 *			'State' => 'BC'
	 *			'Title' => 'Test Title'
	 * 		);
	 * 		</code>
	 *
	 * @return boolean true if successful, false otherwise
	 */
    public function post($type, $options)
    {
        
        $leadData = array();
        
        if (in_array($type, array('listing', 'community'))) {
            // Set required fields in config
            if ($type == 'listing') {
                $this->setListingRequired();
            } elseif ($type == 'community') {
                $this->setCommunityRequired();
            }
            
            // Connect to SOAP server
            $soap = $this->connect();
            
            // Process options and build lead data
            if (!empty($options) && is_array($options)) {
                // Make sure we have all required fields
                $required = $this->getRequiredFields();
                if (!empty($required) && is_array($required)) {
                    foreach ($required as $require) {
                        if (empty($options[$require])) {
                            $this->setError("Required Field Missing");
                            break;
                        }
                    }
                }
                                
                foreach ($options as $key => $val) {
                    if (!empty($val)) {
                        $config = $this->parameterConfig[$key];
                        if (!empty($config['type'])) {
                            settype($val, $config['type']);
                        }
                        if (!empty($config['length']) && is_int($config['length'])) {
                            $val = substr($val, 0, $config['length']);
                        }
                        
                        // Add to LeadData
                        $leadData[$key] = $val;
                    }
                }
                
                // No Errors! Submit to SOAP server
                $errors = $this->getError();
                if (empty($errors)) {
                    $resp = $soap->SubmitLead(array(
                        'partner'   => $this->partnerID,
                        'token'     => $this->token,
                        'lead'      => $leadData
                    ));
                    
                    if (!$resp->SubmitLeadResult->ResponseOK) {
                        $this->setError('Error '.implode(',', (array)$resp->SubmitLeadResult->ErrorMessages));
                    } else {
                        return true;
                    }
                }
            } else {
                $this->setError("Invalid Options");
            }
        } else {
            $this->setError("Invalid Type");
        }
        return false;
    }
    
    /*
	 * Returns the last error that occurred
	 * @return string
	 */
    public function getError()
    {
        return $this->error;
    }
    
    /*
	 * Populate the error variable
	 * @param string $error
	 * @return void
	 */
    private function setError($error)
    {
        $this->error = $error;
    }
    
    /*
	 * Connects to the SOAP server for the lead posting
	 * @return object
	 */
    private function connect()
    {
        return new \SoapClient($this->soapURL, array(
            'soap_version' => $this->soapVersion,
            'trace' => true
        ));
    }
        
    /*
	 * Sets the necessary required config fields if it is a community lead
	 * @return void
	 */
    private function setCommunityRequired()
    {
        $this->parameterConfig['BuilderId']['required'] = true;
        $this->parameterConfig['CommunityId']['required'] = true;
    }
    
    /*
	 * Sets the necessary required config fields if it is a listing lead
	 * @return void
	 */
    private function setListingRequired()
    {
        $this->parameterConfig['ListingIds']['required'] = true;
    }
    
    /*
	 * Returns a list of keys for the required fields defined $paramaterConfig
	 * @return array $requiredFields
	 */
    private function getRequiredFields()
    {
        $requiredFields = array();
        foreach ($this->parameterConfig as $key => $val) {
            if ($val['required']) {
                $requiredFields[] = $key;
            }
        }
        return $requiredFields;
    }
}
