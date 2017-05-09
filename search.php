<?php
    function charToBool($char)
    {
        if($char == null){
            return null;
        }
        elseif($char == '1'){
            return true;
        }
        else {
            return false;
        }
    }
    $firstname = $_GET['firstName'];
    $lastname = $_GET['lastName'];
    $mysqli = mysqli_connect('localhost', 'root', '', 'attendance');
    //$mysqli = mysqli_connect('mysql.dandevere.com', 'tamxu11', 'Ghmar01!', 'tammyanddancom_wedding');
    if(mysqli_connect_errno()) {//$mysqli->connect_errno
        $errResult = new stdClass();
        $errResult->success = false;
        $errResult->message = "Error with MySQL connection.";  
        $myJSON = json_encode($errResult);
        echo $myJSON; 
    } else {
        // $searchMainGuest = 'SELECT id, firstname, lastname, rehearsal, wedding, food, other, partysize, guestof, extraguestchoice, rsvptime FROM attendance WHERE firstname = "' . $firstname . '" AND lastname = "' . $lastname . '"';
        // $mainGuestResults = $mysqli->query($searchMainGuest);
        $stmt = $mysqli->prepare('SELECT id, firstname, lastname, rehearsal, wedding, food, other, partysize, guestof, extraguestchoice, rsvptime FROM attendance WHERE firstname = ? AND lastname = ?');
        if($stmt == FALSE) { //$mainGuestResults
            $errResult = new stdClass();
            $errResult->success = false;
            $errResult->message = "Error: Prepare failed with MySQL select query in search.php.";  
            $myJSON = json_encode($errResult);
            echo $myJSON; 
        } else {            
            $stmt->bind_param("ss", $firstname, $lastname);            
            $stmt->execute();
            $stmt->bind_result($id, $firstname, $lastname, $rehearsal, $wedding, $food, $other, $partysize, $guestof, $extraguestchoice, $rsvptime);
            //$mainGuest = mysqli_fetch_assoc($mainGuestResults);

            if($stmt->fetch()){//$mainGuestResults->num_rows > 0
                $allResults = new stdClass();
                $allResults->success = true;
                $allResults->id = $id;//$mainGuest['id'];
                $allResults->partySize = $partysize;//$mainGuest['partysize'];
                $allResults->extraGuestChoice = $extraguestchoice;//$mainGuest['extraguestchoice'];

                $mainGuestInfo = new stdClass();
                $mainGuestInfo->FirstName = $firstname;//$mainGuest['firstname'];
                $mainGuestInfo->LastName = $lastname;//$mainGuest['lastname'];
                $mainGuestInfo->RehearsalAccept = charToBool($rehearsal);//charToBool($mainGuest['rehearsal']);
                $mainGuestInfo->WeddingAccept = charToBool($wedding);//charToBool($mainGuest['wedding']);
                $mainGuestInfo->FoodChoice = $food;//$mainGuest['food'];
                $mainGuestInfo->FoodOther = $other;//$mainGuest['other'];
                $allResults->allGuestInfo[0] = $mainGuestInfo;

                $stmt->close();

                $mainGuestPlusValue = $partysize - 1;
                $findOtherGuests = 'SELECT firstname, lastname, rehearsal, wedding, food, other FROM attendance WHERE guestof = ' . $id . ' ORDER BY rsvptime DESC LIMIT ' . $mainGuestPlusValue;         
                $otherGuestResults = $mysqli->query($findOtherGuests);
                $count = 1;


                while($otherGuest = $otherGuestResults->fetch_assoc()) {
                    $otherGuestInfo = new stdClass();
                    $otherGuestInfo->FirstName = $otherGuest['firstname'];
                    $otherGuestInfo->LastName = $otherGuest['lastname'];                
                    $otherGuestInfo->RehearsalAccept = charToBool($otherGuest['rehearsal']);
                    $otherGuestInfo->WeddingAccept = charToBool($otherGuest['wedding']);
                    $otherGuestInfo->FoodChoice = $otherGuest['food'];
                    $otherGuestInfo->FoodOther = $otherGuest['other'];
                    $allResults->allGuestInfo[$count] = $otherGuestInfo;
                    $count = $count + 1;
                }

                $myJSON = json_encode($allResults);
                echo $myJSON; 
            }
            else{
                $errResult = new stdClass();
                $errResult->success = false;
                $errResult->message = "Guest not found! Please enter name of primary guest as written on invitation.";                
                $myJSON = json_encode($errResult);
                echo $myJSON; 
            }
            

            
        }

        $mysqli->close();

        

    }
?>