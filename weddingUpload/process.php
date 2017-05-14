<!DOCTYPE HTML>
<html>
    <head>
    </head>
    <body>
        <?php
        if($_FILES['xlsfile']['name']) {
            if(!$_FILES['xlsfile']['error']) {
                $valid_file = true;
                $new_file_name = strtolower($_FILES['xlsfile']['tmp_name']);
                if($_FILES['xlsfile']['size'] > 1024000) {
                    $valid_file = false;
                    $message = 'The file is too large';
                }
                if($valid_file) {
                    $currentdir = getcwd();

                    $target = $currentdir . '/uploads/' . basename($_FILES['xlsfile']['name']);
                    move_uploaded_file($_FILES['xlsfile']['tmp_name'], $target);
                }
            }
        }
        $servername = "mysql.dandevere.com";
        $username = "tamxu11";
        $password = "Ghmar01!";
        $dbname = "tammyanddancom_wedding";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "CREATE TABLE IF NOT EXISTS Attendance (
        id INT NOT NULL PRIMARY KEY auto_increment,
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(50) NOT NULL,
        rehearsal CHAR(1),
        wedding CHAR(1),
        food VARCHAR(20),
        other VARCHAR(200),
        partysize INT NOT NULL DEFAULT 1,
        guestof INT,
        extraguestchoice INT NOT NULL DEFAULT 0,
        rsvptime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        Constraint UC_Person UNIQUE (firstname,lastname)
        )";
        if($conn->query($sql) === TRUE) {

        } else {
            echo "Error";
        }
        if(($handle = fopen($target, "r")) !=+ FALSE) {

            $stmt = $conn->prepare("INSERT INTO Attendance (firstname, lastname, partysize) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $firstname, $lastname, $partysize);
            while(($data = fgetcsv($handle, ',')) !== FALSE) {
                $firstname = $data[0];
                $lastname = $data[1];
                $partysize = $data[2];
                $stmt->execute();
            }
            $stmt->close();
            $conn->close();
            fclose($handle);
            echo "Success";
        }
        

        ?>
    </body>
</html>