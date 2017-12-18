<?php

    function get($name, $def='')
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $def;
    }

    // IS ACTIVE PAGE
    function is_active($page,$current_link)
    {
        return $page == $current_link ? 'active' : '';
    }

    // Add Contacts Function - ADD COMMENTS LATER
    function addContact()
    {
        if (isset($_POST['submit']) == 'submit') {

            $file_name = '..\app\model\contacts.csv';

            if (!file_exists($file_name)) {
                $handle = fopen($file_name, 'a+');
                $id = 1;
            } else {
                $handle = fopen($file_name, 'a+');
                $csv = array_map('str_getcsv', file($file_name));
                $id = 1;
                // Ensures that a unique id is given for each new contact regardless if a contact has been deleted
                foreach($csv as $row){
                    while(reset($row) >= $id){
                        $id++;
                    }
                }
            }
        }
        // Array of form $_POST form Data
        $filedata = array($id,$_POST['title'],$_POST['fname'],$_POST['lname'],$_POST['email'],
            $_POST['website'],$_POST['cellnum'],$_POST['homenum'],$_POST['officenum'],$_POST['twitter'],
            $_POST['facebook'],$_POST['comments']);

        //Moves picture to another directory and saves it with the id as the name.
        move_uploaded_file($_FILES['picture']['tmp_name'],'..\app\model\contact_images\\'.$id.'.png');
        // Write Form Data to the CSV File as a Row
        fputcsv($handle, $filedata);
        fclose($handle);
    }

    function viewContacts()
    {
        $file_name = '..\app\model\contacts.csv';

        if(!file_exists($file_name)){
            echo "<h1>ERROR OPENING CSV</h1>";
            return null;
        }
        else{
            //Open the file and map out the arrrays using array_map
            $csv = array_map('str_getcsv', file($file_name));

            /*Loops through the array and checks the first element and stores it as the ID.
              The id of the Row is also set for functionality.
              Then the first 4 elements of the contact data are echo'd.
            */
            foreach($csv as $line){
                $id = reset($line);
                echo "<tr id='$id'>";
                for($i = 0; $i < 4; $i++){
                    echo "<td>$line[$i]</td>";
                }
                // Retrieve the image of the contact
                $image_path = '../app/model/contact_images/'.$id.'.png';
                $placeholder_image = '../app/model/contact_images/placeholder.png';

                // Check if an image exists for this contact, if not then use the default image.
                if(file_exists($image_path))
                {
                    echo "<td><img src=$image_path width='125px' height='100px'  /></td>";
                }
                else
                    echo "<td><img src=$placeholder_image width='125px' height='100px'  /></td>";

                echo "<td><a href='?page=modify&id=$id'><button name='modify' class='btn btn-warning'>Modify</button></a></td>";
                echo "<td><form method='post' action='?page=delete&id=$id' onclick=\"return confirm('Are you sure you want to delete this contact?')\"><button name='delete' class='btn btn-danger' >Delete</button></button></form></td></tr>";
            }
        }

    }
    // Function to help pre-populate
    function getContact($id)
    {
        $file_name = '..\app\model\contacts.csv';

        if(!file_exists($file_name)) {
            echo "<h1>ERROR OPENING CSV</h1>";
        }
        else{
            // Maps each line of a csv into an array.
            $csv = array_map('str_getcsv', file($file_name));
            // Loop through the array and return the contact with the matching id.
            foreach($csv as $line){
                if(reset($line) == $id){
                    return $line;
                }
            }
            return null;
        }
    }

    function modifyContact($id)
    {
        $file_name = '..\app\model\contacts.csv';
        $temp_file = '..\app\model\temp.csv';
        // Array of new data that has been modified.
        $contact_data = array($id, $_POST['title'], $_POST['fname'], $_POST['lname'], $_POST['email'],
            $_POST['website'], $_POST['cellnum'], $_POST['homenum'], $_POST['officenum'], $_POST['twitter'],
            $_POST['facebook'], $_POST['comments']);

        $handle = fopen($file_name,'r+');
        $temp_handle = fopen($temp_file,'a+');
        $csv = array_map('str_getcsv', file($file_name));

        // If the id matches then write the new data and the old data to the temp file.
        foreach($csv as $line){
            if(reset($line) == $id){
                fputcsv($temp_handle,$contact_data);
            }
            else
                fputcsv($temp_handle,$line);
        }

        fclose($handle);
        fclose($temp_handle);
        rename('..\app\model\temp.csv','..\app\model\contacts.csv');

        move_uploaded_file($_FILES['picture']['tmp_name'],'..\app\model\contact_images\\'.$id.'.png');
    }

    function deleteContact($id)
    {

        // File Paths
        $file_name = '..\app\model\contacts.csv';
        $temp_file = '..\app\model\temp.csv';

        // Open Files
        $handle = fopen($file_name,'r+');
        $temp_handle = fopen($temp_file,'w+');

        // Maps the file for looping. Each Element is an Array with a call back method.
        $csv = array_map('str_getcsv', file($file_name));

        foreach($csv as $row){
            if(reset($row) !== $id){
                fputcsv($temp_handle,$row);
            }
        }
        fclose($handle);
        fclose($temp_handle);

        // Rename the temp file so that it overwrites and replaces the file contents of the old file.
        rename('..\app\model\temp.csv','..\app\model\contacts.csv');

        // Delete the photo from the images folder
        $photo_dir_path = '..\app\model\contact_images' . DS . $id .'.png';
        if(file_exists($photo_dir_path))
        {
            unlink($photo_dir_path);
        }
    }

    function searchContact($key){

        $file_name = '..\app\model\contacts.csv';
        $csv = array_map('str_getcsv', file($file_name));

        foreach($csv as $line){
            /* Checks if $Key matches the location of first name and last name in the array.
               strcasecmp ensures that upper and lower cases can be compared equally*/
            if(strcasecmp($line[2],$key) ==  0 or strcasecmp($line[3],$key) == 0){
                for($i = 0; $i < 4; $i++){
                    echo "<td>$line[$i]</td>";
                }

                $id = $line[0];

                // Retrieve the image of the contact
                $image_path = '../app/model/contact_images/'.$id.'.png';
                $placeholder_image = '../app/model/contact_images/placeholder.png';

                // Check if an image exists for this contact, if not then use the default image.
                if(file_exists($image_path))
                {
                    echo "<td><img src=$image_path width='125px' height='100px'  /></td>";
                }
                else
                    echo "<td><img src=$placeholder_image width='125px' height='100px'  /></td>";

                echo "<td value='$id' name='modify'><a href='?page=modify&id=$id'><button class='btn btn-warning'>Modify</button></a></td>";
                echo "<td><form method='post' action='?page=delete&id=$id' onclick=\"return confirm('Are you sure you want to delete this contact?')\"><button name='delete' class='btn btn-danger' >Delete</button></button></form></td></tr>";
            }
        }
    }

    function getPhoto($id)
    {
        $directory = '..\app\model\contact_images';
        // If the directory exists Scan the directory for photo matching $id.png
        if(is_dir($directory)){
            $files = scandir($directory);
                foreach($files as $photo){
                    if($photo == ($id.'.png')){
                        return $photo;
                    }
                }
        }
    }

