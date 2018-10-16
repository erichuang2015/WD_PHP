<?php
    include("../header.php");

    if ($_GET){
        if(isset($_GET["code"]) && ($_SESSION[FRAME_NAME]['LINE_TOKEN']==$_GET["state"])){
            
            $ch = curl_init();

            $data = array(
                "grant_type" => "authorization_code",
                "client_id" => $setting->getValue("lineAuthClientID"),
                "client_secret" => $setting->getValue("lineAuthClientSecret"),
                "code" => $_GET["code"],
                "redirect_uri" => HTTP_PATH."include/LineLogin/line_callback.php"
            );

            curl_setopt($ch, CURLOPT_URL, "https://api.line.me/oauth2/v2.1/token");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response, true);
            if(!isset($result["error"])){
                $result = json_decode(base64_decode(explode('.', $result["id_token"])[1]), true);
                $_SESSION[FRAME_NAME]['LINE_LOGIN']["id"] = $result["sub"];
                $_SESSION[FRAME_NAME]['LINE_LOGIN']["name"] = $result["name"];
                $_SESSION[FRAME_NAME]['LINE_LOGIN']["email"] = $result["email"];
                $_SESSION[FRAME_NAME]['LINE_LOGIN']["picture"] = $result["picture"];
                
                echo '
                    <script>
                        if(window.opener.document.location.href.indexOf("?") != -1){
                            window.opener.document.location.href = window.opener.document.URL+"&socialLogin=line";
                        }else{                            
                            window.opener.document.location.href = window.opener.document.URL+"?socialLogin=line";
                        }
                    </script>
                ';
                echo '<script>window.close();</script>';
                exit;
            }else{
                echo '<script>window.opener.alert("登入失敗!!['.$result["error"].']");</script>';
                echo '<script>window.close();</script>';
                exit;
            }
        }
    }
    echo '<script>window.opener.alert("登入失敗!!");</script>';
    echo '<script>window.close();</script>';
    exit;

?>