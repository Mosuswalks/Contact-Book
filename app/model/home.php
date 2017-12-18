<?php
    // On form submition, run addContact Function and Go to View_Contacts
    if(isset($_POST['submit']) == 'submit'){
        addContact();
        header('Location: index.php?page=view_contacts');
    }
