var FiltersEnabled = 0; // if your not going to use transitions or filters in any of the tips set this to 0
var spacer="&nbsp; &nbsp; &nbsp; ";

// email notifications to admin
notifyAdminNewMembers0Tip=["", spacer+"No email notifications to admin."];
notifyAdminNewMembers1Tip=["", spacer+"Notify admin only when a new member is waiting for approval."];
notifyAdminNewMembers2Tip=["", spacer+"Notify admin for all new sign-ups."];

// visitorSignup
visitorSignup0Tip=["", spacer+"If this option is selected, visitors will not be able to join this group unless the admin manually moves them to this group from the admin area."];
visitorSignup1Tip=["", spacer+"If this option is selected, visitors can join this group but will not be able to sign in unless the admin approves them from the admin area."];
visitorSignup2Tip=["", spacer+"If this option is selected, visitors can join this group and will be able to sign in instantly with no need for admin approval."];

// orders table
orders_addTip=["",spacer+"This option allows all members of the group to add records to the 'Orders' table. A member who adds a record to the table becomes the 'owner' of that record."];

orders_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Orders' table."];
orders_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Orders' table."];
orders_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Orders' table."];
orders_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Orders' table."];

orders_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Orders' table."];
orders_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Orders' table."];
orders_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Orders' table."];
orders_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Orders' table, regardless of their owner."];

orders_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Orders' table."];
orders_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Orders' table."];
orders_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Orders' table."];
orders_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Orders' table."];

// contacts table
contacts_addTip=["",spacer+"This option allows all members of the group to add records to the 'Contacts' table. A member who adds a record to the table becomes the 'owner' of that record."];

contacts_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Contacts' table."];
contacts_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Contacts' table."];
contacts_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Contacts' table."];
contacts_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Contacts' table."];

contacts_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Contacts' table."];
contacts_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Contacts' table."];
contacts_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Contacts' table."];
contacts_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Contacts' table, regardless of their owner."];

contacts_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Contacts' table."];
contacts_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Contacts' table."];
contacts_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Contacts' table."];
contacts_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Contacts' table."];

// addresses table
addresses_addTip=["",spacer+"This option allows all members of the group to add records to the 'Addresses' table. A member who adds a record to the table becomes the 'owner' of that record."];

addresses_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Addresses' table."];
addresses_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Addresses' table."];
addresses_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Addresses' table."];
addresses_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Addresses' table."];

addresses_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Addresses' table."];
addresses_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Addresses' table."];
addresses_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Addresses' table."];
addresses_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Addresses' table, regardless of their owner."];

addresses_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Addresses' table."];
addresses_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Addresses' table."];
addresses_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Addresses' table."];
addresses_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Addresses' table."];

// companies table
companies_addTip=["",spacer+"This option allows all members of the group to add records to the 'Companies' table. A member who adds a record to the table becomes the 'owner' of that record."];

companies_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Companies' table."];
companies_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Companies' table."];
companies_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Companies' table."];
companies_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Companies' table."];

companies_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Companies' table."];
companies_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Companies' table."];
companies_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Companies' table."];
companies_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Companies' table, regardless of their owner."];

companies_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Companies' table."];
companies_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Companies' table."];
companies_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Companies' table."];
companies_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Companies' table."];

// logins table
logins_addTip=["",spacer+"This option allows all members of the group to add records to the 'Logins' table. A member who adds a record to the table becomes the 'owner' of that record."];

logins_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Logins' table."];
logins_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Logins' table."];
logins_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Logins' table."];
logins_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Logins' table."];

logins_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Logins' table."];
logins_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Logins' table."];
logins_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Logins' table."];
logins_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Logins' table, regardless of their owner."];

logins_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Logins' table."];
logins_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Logins' table."];
logins_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Logins' table."];
logins_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Logins' table."];

// compnayTypes table
compnayTypes_addTip=["",spacer+"This option allows all members of the group to add records to the 'CompnayTypes' table. A member who adds a record to the table becomes the 'owner' of that record."];

compnayTypes_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'CompnayTypes' table."];
compnayTypes_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'CompnayTypes' table."];
compnayTypes_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'CompnayTypes' table."];
compnayTypes_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'CompnayTypes' table."];

compnayTypes_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'CompnayTypes' table."];
compnayTypes_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'CompnayTypes' table."];
compnayTypes_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'CompnayTypes' table."];
compnayTypes_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'CompnayTypes' table, regardless of their owner."];

compnayTypes_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'CompnayTypes' table."];
compnayTypes_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'CompnayTypes' table."];
compnayTypes_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'CompnayTypes' table."];
compnayTypes_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'CompnayTypes' table."];

// details table
details_addTip=["",spacer+"This option allows all members of the group to add records to the 'Details' table. A member who adds a record to the table becomes the 'owner' of that record."];

details_view0Tip=["",spacer+"This option prohibits all members of the group from viewing any record in the 'Details' table."];
details_view1Tip=["",spacer+"This option allows each member of the group to view only his own records in the 'Details' table."];
details_view2Tip=["",spacer+"This option allows each member of the group to view any record owned by any member of the group in the 'Details' table."];
details_view3Tip=["",spacer+"This option allows each member of the group to view all records in the 'Details' table."];

details_edit0Tip=["",spacer+"This option prohibits all members of the group from modifying any record in the 'Details' table."];
details_edit1Tip=["",spacer+"This option allows each member of the group to edit only his own records in the 'Details' table."];
details_edit2Tip=["",spacer+"This option allows each member of the group to edit any record owned by any member of the group in the 'Details' table."];
details_edit3Tip=["",spacer+"This option allows each member of the group to edit any records in the 'Details' table, regardless of their owner."];

details_delete0Tip=["",spacer+"This option prohibits all members of the group from deleting any record in the 'Details' table."];
details_delete1Tip=["",spacer+"This option allows each member of the group to delete only his own records in the 'Details' table."];
details_delete2Tip=["",spacer+"This option allows each member of the group to delete any record owned by any member of the group in the 'Details' table."];
details_delete3Tip=["",spacer+"This option allows each member of the group to delete any records in the 'Details' table."];

/*
	Style syntax:
	-------------
	[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,
	TextTextAlign,TitleFontFace,TextFontFace, TipPosition, StickyStyle, TitleFontSize,
	TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY,
	TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]

*/

toolTipStyle=["white","#00008B","#000099","#E6E6FA","","images/helpBg.gif","","","","\"Trebuchet MS\", sans-serif","","","","3",400,"",1,2,10,10,51,1,0,"",""];

applyCssFilter();
