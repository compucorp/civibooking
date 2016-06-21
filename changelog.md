The goal of this document is to account for the changes made to the original civi4.6 branch to make future development easier.

### Change 1: Add UI elements to the resource creation form

Under the Administer > Civibooking > Manage Resources the new UI looks like the following:

![alt text](changelogimage.png "New Resource Creation UI")

The changes were made to the civibooking/templates/CRM/Admin/Form/Resource.tpl file and to the civibooking/CRM/Admin/Form/Resource.php file.

The code is fairly straightforward and is well commented.

### Change 2: Link booking form to the back-end
