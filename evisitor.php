<?php

namespace App;

class Evisitor 
{

     //protected $authenticationServiceUrl = "https://www.evisitor.hr/eVisitorRhetos_API/Resources/AspNetFormsAuth/Authentication/"; /* production */

      protected $authenticationServiceUrl = "https://www.evisitor.hr/testApi/Resources/AspNetFormsAuth/Authentication/"; /* TEST */

    //protected $restServiceUrl = "https://www.evisitor.hr/eVisitorRhetos_API/Rest/Htz/"; /* production */

     protected $restServiceUrl = "https://www.evisitor.hr/testApi/Rest/Htz/";  /* TEST */
    
    public function login($username,$password)
    {


                  $data = array("UserName" => $username, "Password" => $password, "PersistCookie" => "false");
                  $data_string = json_encode($data); 
                                                                                                    
                  $resource = "Login";
                  $login_url = $this->authenticationServiceUrl.$resource;

                  /* spajanje curl na evisitor api login */

                  $ch = curl_init($login_url);                                            
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                  curl_setopt($ch, CURLOPT_VERBOSE, 1);
                  curl_setopt($ch, CURLOPT_HEADER, 1);                                                                     
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                         
                      'Content-Type: application/json',                                                                               
                      'Content-Length: ' . strlen($data_string))                                                                      
                  );
                                                                                                                                    
                  $response = curl_exec($ch);
                  $responseCode = curl_getinfo($ch)['http_code'];
                  $cookieContent = $this->get_headers_from_curl_response($response)["Set-Cookie"]; /* cookie content stavi u bazu */

                  //echo "Cookie content=>.$cookieContent."<br>";

                  //var_dump($response);

                  $postvar['cookieses'] = $cookieContent;

                  list($header, $body) = explode("\r\n\r\n", $response, 2);

                  if ($responseCode == "200" && $body == "true") { // uspjesno ulogiran u e visitor
                    
                    $status = "OK";

                    /* napravi tablice koje treba za ovog usera */

                    return response()->json([
                                                'status' => "evisitor ok",
                                                'evistoken' => $cookieContent
                                                ], 200);

                    //return $cookieContent;

                  } else {

                    return response()->json([
                                            'status' => 'nemogu se ulogirati u evisitor'
                                            ], 200);

                    //return 'NA';


                  }
    }


    public  function get_headers_from_curl_response($response)
    {
          $headers = array();
          $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
          foreach (explode("\r\n", $header_text) as $i => $line)
              if ($i === 0)
                  $headers['http_code'] = $line;
              else
              {
                  list ($key, $value) = explode(': ', $line);
            if ($key == "Set-Cookie") {
              $value = str_replace('HttpOnly', '', $value);
            }
            
                  if (isset($headers[$key])){
              $headers[$key] = $headers[$key]." ".$value;
            } else {
              $headers[$key] = $value;
            }
              }

          return $headers;
      }

    public function read_from_file($targetFile) 
    {
        //samo zadnji dio koristiti ako se poziva kroz cli
        //apsolutni put koristiti ako se testira pozivom kroz web server
        $urlTargetFile = $targetFile;

        $xmlfile = fopen($urlTargetFile, "r") or die("Unable to open file!");

        $data = fread($xmlfile,filesize($urlTargetFile));

        fclose($xmlfile);

        return $data;
    }

}