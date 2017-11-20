## CiviBooking

### What is CiviBooking?
Are you a non-profit with Rooms or Resources that you think you could raise money by renting out? Or are you a community center or voluntary service looking to manage your resources better? Sick of spreadsheets, emails and that Google calendar that no one keeps up to date? Then CiviBooking is for you!

CiviBooking is a CiviCRM extension which allows you to:

 - Create a list of resources which are available to be booked (we call these limited resources as once their booked, they're gone!)
 - These are shown in a fancy calendar type screen so you can see what’s available super easily.
 - Contacts in the database can book one of more of these by using the booking wizard.
 - Contacts can add unlimited resources to the bookings (tea's, coffee's, solar energy...)
 - Contacts can add additional charges, discounts and calculate a price based on what’s booked.
 - You can make provisional bookings and come back and edit them later.
 - You can cancel bookings, applying a cancellation charge if necessary.

Oh and of course everything integrates super nicely with search, contact tabs, CiviContribute for payments and some other tricks and tweaks along the way.

### Installing CiviBooking
CiviBooking is a free and open source extension for CiviCRM that can be downloaded from the extensions directory.

To find out more about the extensions directory and how to configure it please see below:

1. Set up and install CiviCRM as you would normally. More details [Here](https://wiki.civicrm.org/confluence/display/CRMDOC/Installation+and+Upgrades)

2. Create and set your extensions directory in CiviCRM at:

  http://example.com/civicrm/admin/setting/path?reset=1

  (You may need to create a folder for your extensions and set the permissions for that folder. See [Here](https://wiki.civicrm.org/confluence/display/CRMDOC43/Extensions) for full instructions and information on how to set and configure extensions.)

3. Once configured, simply got to the "add new" tab in your extensions listing:
http://example.com/civicrm/admin/extensions?reset=1

  Then locate the CiviBooking extension and click Download.

### Overview
Like with all systems, before you configure CiviBooking you will need to do some planning.

CiviBooking has support for “limited” and “unlimited” resources.

Limited resources can only be used by one person at a time, where-as unlimited resources are available to be used multiple times at the same time.

As such limited resources may be Rooms, Projectors or Vans, which once booked to a person are in use, whereas an unlimited resource could be Tea/Coffee or Lunches for example.

Limited resources are shown on page one of the booking wizard with a calendar view, whereas unlimited resources are shown on page 2.

Each resource has a “configuration set” allocated to it. This set can be thought of as the pricing options that are available for each resource. For example you may wish to charge for your room on the basis of the length of time it is used for. You may say that the price for a full day is £110, whereas the price for a half-day is £75. These two prices would make up the configuration options for the set.

The price of a resource is calculated as the:

Configuration option price x Quantity

So if someone books for 2 days, you can simply set the price when booking to be 2 x Full day price.


### Configuration

#### Step 1: Define your configuration sets and options
For each resource you will need to decide on your configuration options. We suggest you start at this step, as without it you will not be able to add resources.

Each configuration option also has a unit. You could choose to make the units whatever you wish, so if you wish to charge by hour, day, person or any other label, the system will allow you to do so.

Start by creating your list of configuration size units. (For example this may be days, people, hours etc.).

Then create a configuration set for Rooms. A configuration set can be used by more then 1 resource. After creating your set, add each pricing option.

#### Step 2: Create your resources
**Resource types:**

Resources can have different types. This defines the group that they will be shown in on the screen. You may wish to group resources by room, or by location depending on what is easiest.

Start by entering in your resource types from the resource type option list.

**Resources:**

Now you are able to add your resources. For each resource you can select a type and a configuration option set.

You can set the resource to be limited or unlimited here.

#### Step 3: Cancellation charges
When cancelling a booking, a wizard page appears allowing you to set a cancellation charge based on the cost of the booking.

This could be based on a percentage of the total cost of the booking and then a manual adjustment could be made.

The % charges for cancellation can be adjusted in the cancellation charges option menu. Enter a number value which corresponds to the % discount charge in the value field.

#### Step 4: Additional (Ad-hoc) charges items
Other charges can be added to the booking through the additional charges items. This may be charges for consumables used during the booking that need to be added after the booking has completed. You can add items and a price for each item in the Additional Charges item menu link.

### Addon
We have also created a Drupal view integration module for CiviBooking. This module creates view handlers for booking entities which enables the display of booking data on the Drupal side. It also has a few built-in calendar view templates which can be used to create calendar views for bookings and resource availability.

https://github.com/compucorp/civiBooking_calendar

### Credit
CiviBooking was developed by Compucorp Ltd with kind funding from the GMCVO, Blackburn with Darwin CVS and Zing Foundation

### Support
If you have issues with the CiviBooking extension please comment on the github issues list Here. Please provide your extension and environment detail in the ticket to help accelarate the investigations.

CiviCRM Extension Page: https://civicrm.org/extensions/civibooking

Please contact the follow email if you have any question: <info@compucorp.co.uk>, <guanhuan@compucorp.co.uk>

Paid support for this extension is available, please contact us either via github or at info@compucorp.co.uk

<br \>

[![Compucorp Ltd.][1]][2]
[1]: https://www.compucorp.co.uk/sites/default/files/logo.png
[2]: https://www.compucorp.co.uk
