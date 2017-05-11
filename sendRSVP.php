<?php
    $mainGuestId = $_GET['mainGuestId'];
    $extraGuestChoice = $_GET['extraGuestChoice'];
    $sendData = $_GET['sendData'];    
    
    
    $mysqli = mysqli_connect('localhost', 'root', '', 'attendance');
    if(mysqli_connect_errno()) {
        $errResult = new stdClass();
        $errResult->success = false;
        $errResult->message = "Error with MySQL connection.";  
        $myJSON = json_encode($errResult);
        echo $myJSON; 
    } else {
        $rehearsalAccept = $sendData[0]["RehearsalAccept"] == 'true' ? '1' : '0';
        $WeddingAccept = $sendData[0]["WeddingAccept"] == 'true' ? '1' : '0';
        /*if($sendData[0]["RehearsalAccept"] == 'true'){
            $rehearsalAccept = '1';
        }
        else{
            $rehearsalAccept = '0';            
        }

        if($sendData[0]["WeddingAccept"] == 'true'){
            $WeddingAccept = '1';
        }
        else{
            $WeddingAccept = '0';            
        }*/

        $FoodChoice = $sendData[0]["FoodChoice"];
        $FoodOther = $sendData[0]["FoodOther"];

        $stmt = $mysqli->prepare('UPDATE attendance SET rehearsal = ?, wedding = ?, food = ?, other = ?, extraguestchoice = ? WHERE id = ?');
        //$updateMainGuest = 'UPDATE attendance SET rehearsal = ' . $rehearsalAccept . ', wedding = ' . $WeddingAccept . ', food = "' . $FoodChoice . '", other = "' . $FoodOther . '", extraguestchoice = ' . $extraGuestChoice . ' WHERE id = ' . $mainGuestId;
        //$mainGuestResults = $mysqli->query($searchMainGuest);
        //$mysqli->query($updateMainGuest);
        if($stmt == FALSE) { //$mainGuestResults
            $errResult = new stdClass();
            $errResult->success = false;
            $errResult->message = "Error: Prepare failed with MySQL select query in search.php.";  
            $myJSON = json_encode($errResult);
            echo $myJSON; 
        }
        else
        {
            $stmt->bind_param("ssssii", $rehearsalAccept, $WeddingAccept, $FoodChoice, $FoodOther, $extraGuestChoice, $mainGuestId);   
            $stmt->execute();
            $stmt->close();

            // $successMessage = new stdClass();
            //     $successMessage->success = true;              
            //     $successJSON = json_encode($successMessage);
            //     echo $successJSON;

            $stmtOtherGuest = $mysqli->prepare('REPLACE INTO attendance (firstname, lastname, rehearsal, wedding, food, other, guestof) values (?, ?, ?, ?, ?, ?, ?)');
            if($stmtOtherGuest == FALSE) { //$mainGuestResults
                $errResult = new stdClass();
                $errResult->success = false;
                $errResult->message = "Error: Prepare failed with MySQL select query in search.php.";  
                $myJSON = json_encode($errResult);
                echo $myJSON; 
            }
            else
            {
                $stmtOtherGuest->bind_param("ssssssi", $FirstNameOtherGuest, $LastNameOtherGuest, $rehearsalAcceptOtherGuest, $WeddingAcceptOtherGuest, $FoodChoiceOtherGuest, $FoodOtherOtherGuest, $mainGuestId);
                
                //first delete previous other guests in db
                $deleteOtherGuests = 'DELETE FROM attendance WHERE guestof = ' . $mainGuestId ;         
                $otherGuestResults = $mysqli->query($deleteOtherGuests);
                
                for($i = 1; $i < count($sendData); $i++)
                {
                    $FirstNameOtherGuest = $sendData[$i]["FirstName"];
                    $LastNameOtherGuest = $sendData[$i]["LastName"];
                    $rehearsalAcceptOtherGuest = $sendData[$i]["RehearsalAccept"] == 'true' ? '1' : '0';
                    $WeddingAcceptOtherGuest = $sendData[$i]["WeddingAccept"] == 'true' ? '1' : '0';
                    $FoodChoiceOtherGuest = $sendData[$i]["FoodChoice"];
                    $FoodOtherOtherGuest = $sendData[$i]["FoodOther"];

                    $stmtOtherGuest->execute();
                    //$insertOtherGuest = 'REPLACE INTO attendance (firstname, lastname, rehearsal, wedding, food, other, guestof) values ("' . $FirstName . '", "' . $LastName . '", ' . $rehearsalAccept . ', ' . $WeddingAccept . ', "' . $FoodChoice . '", "' . $FoodOther . '", ' . $mainGuestId . ')';
                    //$mainGuestResults = $mysqli->query($searchMainGuest);
                    //$mysqli->query($insertOtherGuest);
                }
                $stmtOtherGuest->close();

                $successMessage = new stdClass();
                $successMessage->success = true;              
                $successJSON = json_encode($successMessage);
                echo $successJSON;
            }
        }
        $mysqli->close();        
    }
?>