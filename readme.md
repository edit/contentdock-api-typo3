#  Example for using the contentDock API with TYPO3

This is a contentDock API example project for TYPO3 to sending TYPO3 tables to contentDock Data Container.

**This example only demonstrates how the contentDock API can be used. The project is not for production ready.**


## About contentDock
With the App Construction Kit from contentDock® you can create iOS apps for your business, organization or educational institution - without programming knowledge. 

By default, contentDock® offers a large of different content elements with which you can design your apps.
If these elements are not sufficient for the desired functions of your app, you can develop additional functions and integrate them into your apps with our contentDock® SDK without limits.

Combine the advantages of our construction kit, the workflow and the contentDock® SDK.
Develop your individual functions for the apps of your customers and transforms the apps into exclusive native apps in a few simple steps. 

For more information, please visit:
* [contentDock at a glance](https://www.contentdock.com/en)
* [contentDock for developer](https://www.contentdock.com/en/for-developer)

## Documentation contentDock API
More information about the API:

* [contentDock API Documentation](https://www.contentdock.com/en/documentation/api)


## Usage of this example

1. Clone the repository

* `git clone https://github.com/edit/contentdock-api-typo3.git`


2. Prepare tables
Extend the tables that you want to transfer to contentDock with the field

`ALTER TABLE tbl_name
	add contentdock_id int(11) unsigned DEFAULT '0';`


3. Extend the class `MyDataUtility.php`

Update the properties:

* `$t3TableName` - The TYPO3 table name from which you want to transfer data records to contentDock.
* `$t3TableRepository` - Set here the class path to the table repository like `Your\Extension\Domain\Repository\TableRepository`.
* `$dataContainerID` - Set here the ID from the contentDock data container. This you find in the developer area in the data container configuration.
* `$domainFile` - Set here the Domain like `https://domain.tld`, where contentDock can download the files, that you want save in contentDock.


Check the function `getRecordData`and set here the values for the `$data` array.


4. Install and activate the contentDock extension with the TYPO3 extension manager


5. Configure the contentDock extension in the TYPO3 Extension Manager.

Set here:

* `contentDockj API Key` - The API is disabled after your registration. You can activate the API and generate the API key in your contentDock Account Settings.
* `contentDock client subdomain` - Set your contentDock web address here. Please do NOT set '.contentdock.com'.


6. Now you should see a new backend module "contentDock" in TYPO3 with the functions:

* `Operations` - Shows all requests sent to contentdock.
* `Data Container Structure` - Load the structure from all your configured Data Containers in contentDock.
* `Send records` - Send the TYPO3 table data to the contentDock Data Container. The extension checks whether it is an insert, update or delete request. The extension send one request with the records to the contentDock API
* `Delete all records`- Deletes all records in the contentDock Data Container.
* `Get records from Data Container`- Load all records from the contentDock Data Container.


## Production mode
contentDock processes all API Data record requests (insert, update, delete) asynchronously. You receive a process ID with which you can check whether the process was processed by contentDock. For production you should also use the contentDock Webhook configuration for Datan Containers. You can find this configuration in your contentDock account settings. Here you can define a URL that calls contentDock after processing your API process. So you can integrate the API fully automated into your workflow. You can retrieve the results of a process id from contentDock at any time. So every process can be checked afterwards. You can integrate this into your audits.


# Contributing
We are happy if you are convinced of the contentDock workflow and the associated possibilities. If you have any questions, comments or ideas, please feel free to write us a issue ticket.


# Authors
EDIT GmbH - Germany 


# License
This project is licensed under the MIT License
