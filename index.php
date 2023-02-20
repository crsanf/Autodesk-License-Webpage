<?php

class GetUsers {

  #Lists for looking through
  private $licenselist;
  private $aduserlist;
  
  #Used licenses
  private $issuedlicenses;
  private $usedlicenses;

  function __construct() {

    $this->licenselist = $this->getLicenseList();
    $this->aduserlist = $this->getAdUserList();
    $this->issuedlicenses = 0;
    $this->usedlicenses = 0;

  }

  private function getLicenseList() {

    exec("LMUTIL_PATH & LICENSE_FILE_PATH", $output);

    return $output;

  }

  private function getAdUserList() {
    #Account lookup details
    $attributes = array("displayname", "samaccountname");
    $filter     = "(objectCategory=CN=Person,CN=Schema,CN=Configuration,DOMAIN_NAME_HERE)";

    #ldap information
    $ldapserver = "YOUR_SERVER_HERE";
    $ldapuser = "LDAP_USER_HERE";
    $ldappassword = "LDAP_USER_PASSWORD_HERE";
    $ldaptree = "LDAP_TREE_PATH_HERE";
    
    #Connect ldap
    $ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");
    
    //Might need to add ldap_set_option after ldap_connect and ldap_bind however that only allows for the rename feature
    
    #Bind to ldap server
    if($ldapconn) {
      
      $ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappassword) or die("Could not bind to LDAP server.");
      
      if($ldapbind) {
        
        $result = ldap_search($ldapconn, $ldaptree, $filter, $attributes);
        
        $entries = ldap_get_entries($ldapconn, $result);
        
        return $entries;
        
      }
    
    }

    /*
    The above operation failed
    */
    else {
      
      return -1;
      
    }
  
  }

  public function getUsersForLicense($applicationcode, $applicationyear) {

    #Variables and initializing them
    $list = $this->licenselist;

    #Local function variables, of same name as global variables of the class
    $issuedlicenses = 0;
    $usedlicenses = 0;

    foreach($list as $row => $data) {

      #Application is found in the list of license output data
      if(preg_match("~Users of $applicationcode~", $data) == 1) {

        #Get license usage and license totals for this application
        $issuedlicenses = $this->getIssuedLicenses($data);
        $usedlicenses = $this->getUsedLicenses($data);

        #Must run these two lines before a break occurs and the information is lost
        $this->usedlicenses += $usedlicenses;

        if($applicationyear == "2017") {

          $this->issuedlicenses = $issuedlicenses;

        }

        /*
        If no licenses are in used we don't want to check the entire list but we want
        to be able to get the license count data
        */
        if($usedlicenses == 0) {

          #Extra code necessary?
          break;

        }

        for($i = $row + 5; $i < $row + 5 + $issuedlicenses; $i++) {

          /*  
          Break in case of an empty line, precautionary
          */
          if(!$list[$i]) {

            break;

          }

          $username = substr(trim($list[$i]),0,4);
          $fullname = $this->getFullName($username);
          echo "$fullname ($applicationyear) <br/>";

        }

        #We've done everything we need to do, the foreach loop can end
        break;

      }

    }

  }

  #Using ~REGEX~ as delimiters
  private function getIssuedLicenses($data) {

    $splitstring = preg_split("~Total of ~",$data);

    return trim(substr($splitstring[1],0,2));

  }

  #Using ~REGEX~ as delimiters
  private function getUsedLicenses($data) {

    $splitstring = preg_split("~Total of ~",$data);

    return trim(substr($splitstring[2],0,2));

  }

  private function getFullName($username) {

    $aduserlist = $this->aduserlist;

    #We have a valid username list from active directory
    if($aduserlist != -1) {

      $aduserlistlength = count($aduserlist);

      for($i = 0; $i < $aduserlistlength; $i++) {

        if($aduserlist[$i]["samaccountname"][0] == $username) {

          return $aduserlist[$i]["displayname"][0];

        }

      }

    }

    #We don't have a username list from active directory. Print username instead
    else {

      return $username;

    }

  }

  public function printCount() {

    $tempstring = $this->usedlicenses . " of " . $this->issuedlicenses . " in use.";

    echo "<br>$tempstring";

    $this->issuedlicenses = 0;
    $this->usedlicenses = 0;

  }

} //End of Class

?>

<!DOCTYPE html>

<body>

  <header>

  <meta charset="UTF-8">

    <title>Autodesk Network License Usage</title>
  
  <link rel="stylesheet" type="text/css" href="stylesheet/main.css">
  
  </header>
  
  <main>
  
    <h1>Autodesk Network License Usage</h1>
  
  <?php $getusers = new GetUsers(); ?>

  <div class="row">
  
    <div class="column">

      <ul class="results">
        <h2>3D Studio Max</h2>

        <li><?php $getusers->getUsersForLicense("87072", "2019"); ?></li>
        <li><?php $getusers->getUsersForLicense("86833", "2018"); ?></li>
        <li><?php $getusers->getUsersForLicense("86633", "2017"); ?></li>
        <li><?php $getusers->printCount(); ?></li>
    
      </ul>
  
    </div>

    <div class="column">

      <ul class="results">
        <h2>Maya</h2>
      
        <li><?php $getusers->getUsersForLicense("87047", "2019"); ?></li>
        <li><?php $getusers->getUsersForLicense("86884", "2018"); ?></li>
        <li><?php $getusers->getUsersForLicense("86618", "2017"); ?></li>
        <li><?php $getusers->printCount(); ?></li>
    
      </ul>

    </div>

    <div class="column">

      <ul class="results">
        <h2>Motion Builder</h2>
      
        <li><?php $getusers->getUsersForLicense("87077", "2019"); ?></li>
        <li><?php $getusers->getUsersForLicense("86885", "2018"); ?></li>
        <li><?php $getusers->getUsersForLicense("86623", "2017"); ?></li>
        <li><?php $getusers->printCount(); ?></li>
    
      </ul>


    </div>

    <div class="column">

      <ul class="results">
        <h2>Mudbox</h2>
      
        <li><?php $getusers->getUsersForLicense("87078", "2019"); ?></li>
        <li><?php $getusers->getUsersForLicense("86886", "2018"); ?></li>
        <li><?php $getusers->getUsersForLicense("86624", "2017"); ?></li>
        <li><?php $getusers->printCount(); ?></li>
    
      </ul>

    </div>
    
    </div>
  
  </main>
  
  <footer>

    Created by your <a href="https://yourITTeam.me">IT Department</a>
  
  </footer>

</body>
