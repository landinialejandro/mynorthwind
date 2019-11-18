# My Nothwind adminLTE for AppGini

![Login Page](https://trello-attachments.s3.amazonaws.com/5cf458a4c077516299941bbf/600x295/4543ab3b3cfe3a5e98ce23f3f76f0ff2/imagen.png)

## how to use

install and use

## FIX

- 18/11/2019 several changues

- 01/10/2019 fix PREPEND_PATH in files sources

- 04/08/2019 - fix double wide left side menu in small devices

- 29/06/2019 - fix print problem

older

- fix mpi control
- remove side bar in login
- redirection in forgotten password
- redirection in new user registration

## Changes

- Enviroment varible editor

- Add the template like a plugin

- All files for LTE template are in a your folder

- Add back to login button in upper menu in reset password page and new user page.

- Add a right side control bar, ther you can changue the layout color and other things. You can customize this item.

- Update to adminLTE 2.4.10
`

- myCustom.css
  - modification of the background image and add exmples images

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

## New Features

- control right side bar with more functions. Add editro for enviromete varibles

- hidden group

    If the hidden group is created, the tables that are within this group will only be visible to the admin user group.
- mpi

    The mebership_profile_image is added by default, so the membership_profile.php file is also modified

## Resources

- <https://adminlte.io/themes/AdminLTE/documentation/index.html#introduction>
