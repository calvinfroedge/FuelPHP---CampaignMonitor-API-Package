# Campaign Monitor Package

## Installing

Currently only available as download or clone from Github. Like any other package it must be put in its own 'campaignmonitor' dir in the packages dir and added to your app/config/config.php as an always loaded package.

Make sure you put your API key in the config/campaignmonitor.php

## Usage

All calls will return an object with the following properties on success, where status is success or failure, http_response is a numeric http response, and data is the data from the API:

http_response_code, status, data

data will vary by method.

On failure, the following will be returned:

http_response_code, status, data

data will be an array with a code and a message.  The code is an error code specific to CampaignMonitor API, as is the message. 

```php
// Get your clients:
CampaignMonitor::clients('GET');
```

This should return on success a numerically indexed array with 'clientid' and 'name' as keys in each array.  The clientid and the name are the values.

```php
// Get all lists for a particular client:
CampaignMonitor::clients('GET', 'your_list_id', 'lists');
```

This should return on success a numerically indexed array with 'listid' and 'name' as keys in each array.  The listid and the name are the values.

```php
// Create a new subscriber:
$subscriber_details = array(
	'EmailAddress'	=>	'calvinfroedge@gmail.com',
	'Name'	=>	'Calvin Froedge',
	'Resubscribe'	=>	true
);
CampaignMonitor::subscribers('POST', 'your_list_id', $subscriber_details);
```

This should return on success a 201 'Created'.

```php
// Update a subscriber:
$subscriber_details = array(
	'EmailAddress'	=>	'calvinfroedge@gmail.com',
	'Name'	=>	'Calvin Froedge',
	'Resubscribe'	=>	true
);
$update_params = array(
	'qs_params' => array(
		'email'	=>	'calvinfroedge@gmail.com'
	)
);
CampaignMonitor::subscribers('PUT', 'the_subscriber_id', $subscriber_details, $update_params);
```

This should return on success a 200 'OK'.

## LICENSE: 

Copyright (c) 2011 Calvin Froedge

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
