Welcome to the CodeMojo SDK (Startup Development Toolkit)

Using the SDK is straight forward and simple. The SDK consist of three main services

1. [Authentication Service](wiki/Authentication-Service)
2. [Wallet Service](wiki/Wallet-Service)
3. [Loyalty Service](wiki/Loyalty-Service)
4. [Meta Tagging Service](wiki/Meta-Service) 

###Authentication Service###
Authentication service is the basic requirement for all the other services. The constructor of this service takes in 2 parameters, `client id` and `client secret`

**Dependency** None

**Usage**

```php
use DRewards\Client\Services\AuthenticationService;

$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET);
```
###Wallet Service###
You can use wallet service to work on a raw level transactions of crediting and debiting to user accounts

**Dependency** [Authentication Service](wiki/Authentication-Service)

**Usage**

```php
use DRewards\Client\Services\WalletService;

$walletService = new WalletService($authService);
```

###Loyalty Service###
Loyalty service adds logical layer to the wallet service which makes sense to manipulate rewards based on different criteria

**Dependency** [Authentication Service](wiki/Authentication-Service), [Wallet Service](wiki/Wallet-Service)

**Usage**

```php
use DRewards\Client\Services\LoyaltyService;

$loyaltyService = new LoyaltyService($authService);
```

###Meta Tagging Service###
Meta Tagging service is a fully maintained high-performance key-value store as a service that lets you store meta information/object of any kind. The service scales as you grow, so now you can focus on what matters the most.

**Dependency** [Authentication Service](wiki/Authentication-Service)

**Usage** 

```php
$metaService = new MetaService($authService);

$data = ['id'=>1, 'action_id'=>2032, 'stamp'=> time(), 'session'=>'asklj2h91298899003' ];

$metaService->add("item1", json_encode($data));

$previousSession = $metaService->get("item2");

// The service automagically unwraps json/serialized strings to objects
// Now you can focus on what matters the most
$cartPending = $previousSession->cart_pending;

echo 'You have ' . $cartPending . ' items pending from your previous visit!';

// Delete a meta
$metaService->delete("item2");

```
### Example usage of Loyalty ###
Once you have enabled and configured the Loyalty module from your CodeMojo dashboard, you can use it as simple as shown below

```php    
require_once '../sdk/autoload.php';

const CLIENT_ID     = 'sample@codemojo.io';
const CLIENT_SECRET = 'PLB6DHP7VcykRDdvloi2X9tEq3FvsIBhtdn7UdeQ';

// Create an instance of Authentication Service
$authService = new AuthenticationService(CLIENT_ID, CLIENT_SECRET, Endpoints::SANDBOX);

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

