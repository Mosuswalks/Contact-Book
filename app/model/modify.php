<?php
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        // $Contact stores the data used to pre-populate the form
        $contact = getContact($id);
    }
    if(isset($_POST['update']) == 'update'){
        modifyContact($id);
        header('Location: index.php?page=view_contacts');
    }


