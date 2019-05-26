
# how to use

Before pasting this codes files, rename the next files:
| original name | renamed file |
|---------------|--------------|
| index.php | index_old.php |
| footer.php | footer_old.php |
| header.php | header_old.php |
| membership_profile.php | membership_profile_old.php |

Then add the files and use.

Allways after compiling you need to repeat the step of renaming the  files, and re-pasting files in table

you can conmute to default appgini only changue true to false in config_lte.php the varible ```$LTE_enable```

```php
function getLteStatus($LTE_enable = true){
    if(!function_exists('getMemberInfo')){
        $LTE_enable = false;
    } 
    return $LTE_enable ;
}
```

### FIX:
- redirection in forgotten password
- redirection in new user registration

### Changes:
- config_lte.php 
    - ```$LTE_globals``` basic configurations in global variable for the menu and the footer.
    - ```$ico_menu``` definition of icons for lso groups in the side menu, is in json format.
- myCustom.css
    - modification of the background image
        ```css
        .content-wrapper {
            min-height: 100%;
            background-color: #ecf0f5;
            background: url(background/slide_2.jpg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            z-index: 800;
        }
        ```

    - hide fields directly id


        ```css
        label[for='id']{
            display: none;
        }

        label[for='id'] + div {
            display: none;
        }
        ```

# New Features!

- hidden group

    If the hidden group is created, the tables that are within this group will only be visible to the admin user group.
- mpi

    The mebership_profile_image is added by default, so the membership_profile.php file is also modified