### About

Welcome to the CodeMojo SDK (Startup Development Toolkit). CodeMojo helps you to rapidly deploy application components as pluggable services for your PHP application allowing you to cut development time ramp up productivity.

Using the [SDK](https://github.com/codemojo-dr/php-sdk/archive/master.zip) is straight forward and simple. The SDK consist of four main services

1. [Authentication Service](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Authentication-Service)
2. [Wallet Service](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Wallet-Service)
3. [Loyalty Service](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Loyalty-Service)
4. [Meta Tagging Service](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Meta-Service) 

Download the PHP SDK [here](https://github.com/codemojo-dr/php-sdk/archive/master.zip)

Download the **Sample Application** [here](https://github.com/codemojo-dr/startkit-php-sample/archive/master.zip)

See the [Wiki](https://github.com/codemojo-dr/startkit-php-sdk/wiki) for additional documentation

See the [Dashboard walkthrough](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Dashboard) for insights on how to use the dashboard

### Installation & Usage

**Stock/Vanilla PHP**

Download the SDK and include the `autoload.php` to your source code

**Composer**

Simply add the following to your `composer.json`

```json
{
  "require": {
    "codemojo/startkit": "0.1.*"
  }
}
```

### Example
Once you have enabled and configured the Loyalty module from your [CodeMojo dashboard](https://dashboard.codemojo.io), you can use it as simple as shown below. More documentation on [dashboard](https://github.com/codemojo-dr/startkit-php-sdk/wiki/Dashboard).

```php    
require_once '../sdk/autoload.php';

const CLIENT_ID     = 'sample@codemojo.io';
const CLIENT_SECRET = 'PLB6DHP7VcykRDdvloi2X9tEq3FvsIBhtdn7UdeQ';

// Create an instance of Authentication Service
$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET, Endpoints::LOCAL, function($type, $message){
    switch($type){
        case Exceptions::AUTHENTICATION_EXCEPTION:
            echo 'Authentication Exception';
            break;
        case Exceptions::BALANCE_EXHAUSTED_EXCEPTION:
            echo 'Low balance';
            break;
        case Exceptions::FIELDS_MISSING_EXCEPTION:
            echo 'Fields missing';
            break;
        case Exceptions::QUOTA_EXCEPTION:
            echo 'Quota Exhausted Exception';
            break;
        case Exceptions::TOKEN_EXCEPTION:
            echo 'Invalid token Exception';
            break;
        default:
            echo 'Error ' . $message;
            break;
    }
});

// Create an instance of Loyalty Service - If you have more than one wallet service,
// you can optionally pass in the wallet instance as the second argument
$loyaltyService = new LoyaltyService($authService);

// Credit points to user - You can have different set of rules based on the platform
// For example, you can promote your android app by saying Get 5% more cashback when you transact through the Android app
$status = $loyaltyService->addLoyaltyPoints("user1@codemojo.io", 1500, "android", "", 7, "Cashback for Order no. 1231");

// Get the balance in the wallet
$balance = $loyaltyService->getBalance("user1@codemojo.io");

// Check how much maximum can be redeemed by the user for the given (current) transaction value
// Again, you can have different set of rules for redemption based on the platform
$maximumRedemption = $loyaltyService->maximumRedemption("user1@codemojo.io",8500);

// Redeem amount
// (user_id, redemption_amount, current_transaction_value, platform)
$loyaltyService->redeem("user1@codemojo.io", 500, 8500, "android");

```
For more details on the methods & parameters available for each service, take a look at the individual service pages.

