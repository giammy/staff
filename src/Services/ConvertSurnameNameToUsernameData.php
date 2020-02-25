<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConvertSurnameNameToUsernameData {

    private $params;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }
    
    public function convert($surnameName, $surnameArg, $nameArg) {
       //var_dump($surnameName);exit;
       //var_dump($this->exceptionsArray);exit;

       // each array element is an array with 2 elements: [ "SURNAME NAME" , "username" ] 
       $exceptionsArray = array_map(
           function($x) { return preg_split('/, */', $x); },
           preg_split('/; */', $this->params->get('staff_import_exceptions'))
       );
       $exceptArray = array();

       foreach ($exceptionsArray as $value) {
           $exceptArray[$value[0]] = $value[1];
       }

       $spacePos = strpos($surnameName, ' ');
       $username = strtolower(substr($surnameName, 0, $spacePos));
       $username2= strtolower($surnameName[$spacePos+1] . substr($surnameName, 0, $spacePos));
       if (array_key_exists($surnameName, $exceptArray)) {
       	   $username = $exceptArray[$surnameName];
       }

       $usernameData['username'] = $username;
       $attributes = $this->getLDAPAttributes($username,
                                              $this->params->get('ldap_server'), 
                                              $this->params->get('ldap_server_port'), 
                                              $this->params->get('ldap_user'), 
                                              $this->params->get('ldap_password'), 
                                              $this->params->get('ldap_search_basedn'));

       if ($surnameArg != null && $nameArg != null) {
           if ($attributes['surname'] != $surnameArg || $attributes['name'] != $nameArg) {
               $usernameData['username'] = $username2;
               $attributes = $this->getLDAPAttributes($username2,
                                              $this->params->get('ldap_server'), 
                                              $this->params->get('ldap_server_port'), 
                                              $this->params->get('ldap_user'), 
                                              $this->params->get('ldap_password'), 
                                              $this->params->get('ldap_search_basedn'));
           }
       }

       $usernameData['name'] = $attributes['name'];
       $usernameData['surname'] = $attributes['surname'];
       $usernameData['email'] = $attributes['email'];
       $usernameData['telephonenumber'] = $attributes['telephonenumber'];
       return $usernameData;
    }

    protected function getLDAPAttributes($username, $ldapServer, $ldapServerPort, 
                                         $ldapUser, $ldapPassword, $ldapSearchBaseDn) {
	$ldapSearchFilter = "(cn=" . $username . ")";
	$attributes['name'] = "noname";
	$attributes['surname'] = "nosurname";
	$attributes['email'] = "nomail@nomail.com";
	$attributes['telephonenumber'] = "";

	$ds=ldap_connect($ldapServer, $ldapServerPort);
	$r=ldap_bind($ds, $ldapUser, $ldapPassword);
        $returnError = ldap_errno($ds);
        if (! $r) {
    	    // echo ldap_err2str( $returnError );
            //  echo " (Error code: ".$returnError.")\n";
            ldap_close($ds);
	        return $attributes;
        }

	$sr=ldap_search($ds, $ldapSearchBaseDn, $ldapSearchFilter);  
	$info = ldap_get_entries($ds, $sr);

	//var_dump($username);
	//var_dump(count($info));

        //try {
	  if (count($info) > 1) {
	    $attributes['name'] = $info[0]['givenname'][0];
	    $attributes['surname'] = $info[0]['sn'][0];
	    if (array_key_exists ('mail', $info[0])) {
                $attributes['email'] = $info[0]['mail'][0];
            }
	    if (array_key_exists ('telephonenumber', $info[0])) {
                $attributes['telephonenumber'] = $info[0]['telephonenumber'][0];
            }
          }
        //}
	return $attributes;
    }   

}

